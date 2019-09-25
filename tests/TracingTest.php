<?php

use PHPUnit\Framework\TestCase;

use OpenTelemetry\Tracing\{SpanContext, Tracer};

class TracingTest extends TestCase
{
    public function testContext()
    {
        $spanContext = SpanContext::generate();
        $this->assertSame(strlen($spanContext->getTraceId()), 16);
        $this->assertSame(strlen($spanContext->getSpanId()), 8);

        $spanContext2 = SpanContext::generate();
        $this->assertNotSame($spanContext->getTraceId(), $spanContext2->getTraceId());
        $this->assertNotSame($spanContext->getSpanId(), $spanContext2->getSpanId());

        $spanContext3 = SpanContext::restore($spanContext->getTraceId(), $spanContext->getSpanId());
        $this->assertSame($spanContext3->getTraceId(), $spanContext->getTraceId());
        $this->assertSame($spanContext3->getSpanId(), $spanContext->getSpanId());
    }

    public function testTracerRestore()
    {
        $tracer = new Tracer();
        $spanContext = $tracer->getActiveSpan()->getSpanContext();

        $spanContext2 = SpanContext::restore($spanContext->getTraceId(), $spanContext->getSpanId());
        $tracer2 = new Tracer($spanContext2);

        $this->assertSame($tracer->getActiveSpan()->getSpanContext()->getTraceId(), $tracer2->getActiveSpan()->getSpanContext()->getTraceId());
    }

    public function testTracerSpanFork()
    {
        $tracer = new Tracer();
        $global = $tracer->getActiveSpan();

        $mysql = $tracer->createSpan('mysql');
        $this->assertSame($tracer->getActiveSpan(), $mysql);
        $this->assertSame($global->getSpanContext()->getTraceId(), $mysql->getSpanContext()->getTraceId());
        $this->assertSame($mysql->getParentSpanContext(), $global->getSpanContext());
        $mysql->end();

        $this->assertTrue($mysql->getStatus()->isOk());
        
        // active span rolled back
        $this->assertSame($tracer->getActiveSpan(), $global);
        $this->assertNull($global->getStatus());
        
        // active span should be kept for global span
        $global->end();
        $this->assertSame($tracer->getActiveSpan(), $global);
        $this->assertTrue($global->getStatus()->isOk());
    }
}