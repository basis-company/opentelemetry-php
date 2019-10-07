<?php

declare(strict_types=1);

namespace OpenTelemetry\Tracing;

abstract class Exporter
{
    abstract public function convert(Span $span) : array;

    public function flush(Tracer $tracer, Transport $transport) : int
    {
        $data = [];

        foreach ($tracer->getSpans() as $span) {
            $data[] = $this->convert($span);
        }

        if (count($data)) {
            $transport->write($data);
        }

        return count($data);
    }
}