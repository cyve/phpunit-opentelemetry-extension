# PHPUnit OpenTelemetry extension
PHPUnit extension for test tracing

## Installation

```bash
composer require --dev cyve/phpunit-opentelemetry-extension open-telemetry/exporter-otlp
```

## Configuration
```xml
# phpunit.xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit>
   <!-- ... -->
    <extensions>
        <bootstrap class="Cyve\OpenTelemetry\Phpunit\OpenTelemetryExtension"/>
    </extensions>
</phpunit>
```

```
# .env.test
OTEL_SERVICE_NAME=service
OTEL_EXPORTER_OTLP_PROTOCOL=http/json
OTEL_EXPORTER_OTLP_ENDPOINT=http://localhost:4318
```

- `OTEL_SERVICE_NAME` is the name of your application
- `OTEL_EXPORTER_OTLP_PROTOCOL` is any OTEL protocol
- `OTEL_EXPORTER_OTLP_ENDPOINT` is the storage endpoint (ex: Jaeger)

## Usage

Run PHPUnit tests as usual.
