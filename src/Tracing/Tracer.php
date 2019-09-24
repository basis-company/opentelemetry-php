<?php

namespace OpenTelemetry\Tracing;

class Tracer
{
    private $active;
    private $tail = [];

    private $spans = [];

    public function __construct(Context $context = null)
    {
        $context = $context ?: Context::generate();
        $this->active = $this->generateSpanInstance('tracer', $context);
    }

    public function getActiveSpan() : Span
    {
        while (count($this->tail) && $this->active->getEnd()) {
            $this->active = array_pop($this->tail);
        }
        return $this->active;
    }

    public function setActive(Span $span) : Span
    {
        $this->tail[] = $this->active;
        return $this->active = $span;
    }

    public function createSpan(string $name) : Span
    {
        $parent = $this->getActiveSpan()->getContext();
        $context = Context::fork($parent->getTraceId());
        $span = $this->generateSpanInstance($name, $context);
        return $this->setActive($span);
    }

    private function generateSpanInstance($name, Context $context) : Span
    {
        $parent = null;
        if ($this->active) {
            $parent = $this->getActiveSpan()->getContext();
        }
        $span = new Span($name, $context, $parent);
        $this->spans[] = $span;
        return $span;
    }
}