<?php

use PHPUnit\Framework\TestCase;

use OpenTelemetry\Tracing\{Context, Tracer};

class TracingTest extends TestCase
{
    public function testContext()
    {
        $context = Context::generate();
        $this->assertSame(strlen($context->getTraceId()), 16);
        $this->assertSame(strlen($context->getSpanId()), 8);

        $context2 = Context::generate();
        $this->assertNotSame($context->getTraceId(), $context2->getTraceId());
        $this->assertNotSame($context->getSpanId(), $context2->getSpanId());

        $context3 = Context::restore($context->getTraceId(), $context->getSpanId());
        $this->assertSame($context3->getTraceId(), $context->getTraceId());
        $this->assertSame($context3->getSpanId(), $context->getSpanId());
    }

    public function testTracerRestore()
    {
        $tracer = new Tracer();
        $context = $tracer->getActiveSpan()->getContext();

        $context2 = Context::restore($context->getTraceId(), $context->getSpanId());
        $tracer2 = new Tracer($context2);

        $this->assertSame(
            $tracer->getActiveSpan()->getContext()->getTraceId(),
            $tracer2->getActiveSpan()->getContext()->getTraceId(),
        );
    }

    public function testTracerSpanFork()
    {
        $tracer = new Tracer();
        $global = $tracer->getActiveSpan();

        $tarantool = $tracer->createSpan('tarantool');
        $this->assertSame($tracer->getActiveSpan(), $tarantool);
        $this->assertSame($global->getContext()->getTraceId(), $tarantool->getContext()->getTraceId());
        $this->assertSame($tarantool->getParent(), $global->getContext());
        $tarantool->end();

        $this->assertSame($tracer->getActiveSpan(), $global);
        
        $global->end();
        $this->assertSame($tracer->getActiveSpan(), $global);
    }
}