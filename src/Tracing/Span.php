<?php

namespace OpenTelemetry\Tracing;

class Span
{
    private $name;
    private $context;
    private $parent;

    private $start;
    private $end;

    private $status;

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

    public function end(Status $status = null)
    {
        $this->end = microtime(1);
        if (!$this->status && !$status) {
            $status = new Status(Status::OK);
        }
        if ($status) {
            $this->setStatus($status);
        }
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setStatus(Status $status) : self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus() : ?Status
    {
        return $this->status;
    }
}