# OpenTelemetry php library
[![Build Status](https://travis-ci.org/basis-company/opentelemetry-php.svg?branch=master)](https://travis-ci.org/basis-company/opentelemetry-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/basis-company/opentelemetry-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/basis-company/opentelemetry-php/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/basis-company/opentelemetry-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/basis-company/opentelemetry-php/?branch=master)

- [Installation](#installation)
- [Tracing](#tracing)

## Installation
The recommended way to install the library is through [Composer](http://getcomposer.org):
```bash
$ composer require basis-company/opentelemetry
```

## Tracing
Library is under active development, but simple example should be present in readme.  
In addition, see tracing tests for full-featured example.
```php
<?php

use OpenTelemetry\Tracing\Builder;
use OpenTelemetry\Tracing\SpanContext;

$spanContext = SpanContext::generate(); // or extract from headers

$tracer = Builder::create()->setSpanContext($spanContext)->getTracer();

// start a span, register some events
$span = $tracer->createSpan('session.generate');

// set attributes as array
$span->setAttributes([ 'remote_ip' => '5.23.99.245' ]);
// set attribute one by one
$span->setAttribute('country', 'Russia');

$span->addEvent('found_login', [
  'id' => 67235,
  'username' => 'nekufa',
]);
$span->addEvent('generated_session', [
  'id' => md5(microtime(true))
]);

$span->end(); // pass status as an optional argument
```

# Testing
To make sure the tests in this repo work as you expect, you can use the included docker test wrapper:

1.)  Make sure that you have docker installed
2.)  Execute `script/dockertest` from your bash compatible shell.  You should see the test output like so
