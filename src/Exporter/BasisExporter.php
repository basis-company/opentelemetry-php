<?php

declare(strict_types=1);

namespace OpenTelemetry\Exporter;

use OpenTelemetry\Exporter;
use OpenTelemetry\Tracing\Event;
use OpenTelemetry\Tracing\Span;
use OpenTelemetry\Tracing\Status;
use OpenTelemetry\Tracing\SpanContext;
use OpenTelemetry\Tracing\Tracer;

class BasisExporter extends Exporter
{
    public function restoreSpan(array $source): Span
    {
        [$parentSpanId, $traceId, $spanId, $name, $start, $end, $statusCode, $attributes, $events] = $source;
        $context = SpanContext::restore($traceId, $spanId);

        $parent = null;
        if ($parentSpanId) {
            $parent = SpanContext::restore($traceId, $parentSpanId);
        }

        $span = (new Span($name ?: 'tracer', $context, $parent));

        if ($attributes) {
            $span->setAttributes($attributes);
        }

        if ($events) {
            foreach ($events as $info) {
                @[$name, $timestamp, $attributes] = $info;
                $span->addEvent($name, $attributes ?: [], $timestamp);
            }
        }

        $span->setInterval($start, $end);

        if ($statusCode) {
            $span->setStatus(new Status($statusCode));
        }


        return $span;
    }

    public function convertSpan(Span $span): array
    {
        return [
            $span->getParentSpanContext() ? $span->getParentSpanContext()->getSpanId() : null,
            $span->getSpanContext()->getTraceId(),
            $span->getSpanContext()->getSpanId(),
            $span->getName(),
            $span->getStart(),
            $span->getEnd(),
            $span->getStatus() ? $span->getStatus()->getCanonicalCode() : null,
            $span->getAttributes() ?: null,
            array_map([$this, 'convertEvent'], $span->getEvents()) ?: null
        ];
    }

    public function convertEvent(Event $event): array
    {
        $result = [
            $event->getName(),
            $event->getTimestamp(),
        ];

        if (count($event->getAttributes())) {
            $result[] = $event->getAttributes();
        }

        return $result;
    }
}
