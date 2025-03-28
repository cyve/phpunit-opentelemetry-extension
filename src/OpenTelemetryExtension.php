<?php

namespace Cyve\OpenTelemetry\Phpunit;

use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SDK\Sdk;
use OpenTelemetry\SDK\Trace\TracerProviderFactory;
use PHPUnit\Event\Application;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Event;
use PHPUnit\Event\Test;
use PHPUnit\Event\Tracer\Tracer;
use PHPUnit\Metadata\Group;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class OpenTelemetryExtension implements Extension, Tracer
{
    private const TRACER_NAME = 'io.opentelemetry.contrib.phpunit';

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        if (isset($_ENV['OTEL_SERVICE_NAME'], $_ENV['OTEL_EXPORTER_OTLP_PROTOCOL'], $_ENV['OTEL_EXPORTER_OTLP_ENDPOINT'])) {
            $facade->registerTracer($this);
        }
    }

    public function trace(Event $event): void
    {
        static $tracer = $this->initializeTracer();

        static $rootSpan;
        static $rootScope;
        if ($event instanceof Application\Started) {
            $rootSpan = $tracer->spanBuilder('phpunit')->startSpan();
            $rootScope = $rootSpan->activate();

            return;
        }

        static $testSpan;
        static $testScope;
        if ($event instanceof Test\PreparationStarted) {
            /** @var TestMethod $test */
            $test = $event->test();
            $testSpan = $tracer->spanBuilder($test->name())
                ->setAttributes([
                    'className' => $test->className(),
                    'file' => $test->file(),
                    'line' => $test->line(),
                    'groups' => array_map(fn (Group $group) => $group->groupName(), iterator_to_array($test->metadata()->isGroup())),
                ])
                ->startSpan();
            $testScope = $testSpan->activate();

            return;
        }

        if ($event instanceof Test\Finished) {
            $testScope->detach();
            $testSpan->setAttribute('assertions', $event->numberOfAssertionsPerformed())->end();

            return;
        }

        if ($event instanceof Application\Finished) {
            $rootScope->detach();
            $rootSpan->end();
        }
    }

    private function initializeTracer(): TracerInterface
    {
        $tracerProvider = (new TracerProviderFactory())->create(); // create tracer provider from env vars
        Sdk::builder()->setTracerProvider($tracerProvider)->setAutoShutdown(true)->buildAndRegisterGlobal();

        return $tracerProvider->getTracer(self::TRACER_NAME);
    }
}
