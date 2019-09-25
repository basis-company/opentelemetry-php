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

        $this->assertSame($tracer->getActiveSpan()->getContext()->getTraceId(), $tracer2->getActiveSpan()->getContext()->getTraceId());
    }

    public function testTracerSpanFork()
    {
        $tracer = new Tracer();
        $global = $tracer->getActiveSpan();

        $mysql = $tracer->createSpan('mysql');
        $this->assertSame($tracer->getActiveSpan(), $mysql);
        $this->assertSame($global->getContext()->getTraceId(), $mysql->getContext()->getTraceId());
        $this->assertSame($mysql->getParent(), $global->getContext());
        $mysql->end();

        // active span rolled back
        $this->assertSame($tracer->getActiveSpan(), $global);
        
        // active span should be kept for global span
        $global->end();
        $this->assertSame($tracer->getActiveSpan(), $global);
    }
}