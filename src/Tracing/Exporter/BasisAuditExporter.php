<?php

declare(strict_types=1);

namespace OpenTelemetry\Tracing\Exporter;

use OpenTelemetry\Tracing\Exporter;
use OpenTelemetry\Tracing\Span;
use OpenTelemetry\Tracing\Tracer;

class BasisAuditExporter extends Exporter
{
    public function convert(Span $span) : array
    {
        return [
            'body' => serialize($span),
        ];
    }
}
