<?php

namespace OpenTelemetry\Tracing;

class Span
{
    private $name;
    private $context;
    private $parent;

    private $start;
    private $end;

    public function __construct(string $name, SpanContext $context, SpanContext $parent = null)
    {
        $this->name = $name;
        $this->context = $context;
        $this->parent = $parent;
        $this->start = microtime(1);
    }

    public function getSpanContext() : SpanContext
    {
        return $this->context;
    }

    public function getParentSpanContext() : SpanContext
    {
        return $this->parent;
    }

    public function end()
    {
        $this->end = microtime(1);
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }
}