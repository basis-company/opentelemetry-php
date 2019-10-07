<?php

declare(strict_types=1);

namespace OpenTelemetry\Tracing;

interface Transport
{
    public function write(array $data) : bool;
}