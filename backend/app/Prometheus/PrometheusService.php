<?php

namespace App\Prometheus;

use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Exception\MetricNotFoundException;

class PrometheusService
{
    protected $countersNamespace = 'twitter';

    protected $registry;
    protected $counter;
    protected $histogram;

    public function __construct()
    {
        $this->registry = CollectorRegistry::getDefault();
    }

    public function getCounter(string $name, string $description = ''): Counter
    {
        return $this->registry->getOrRegisterCounter(
            $this->countersNamespace,
            $name,
            $description
        );
    }

    public function addResponseTimeHistogram($duration, string $routeName): void
    {
        $this->histogram = $this->registry->getOrRegisterHistogram(
            $this->countersNamespace,
            'http_response_time_seconds',
            'Duration of HTTP response in seconds',
            ['route']
        );

        $this->histogram->observe($duration, [$routeName]);
    }

    public function incrementRequestCounter($routeName): void
    {
        $this->counter = $this->registry->getOrRegisterCounter(
            $this->countersNamespace,
            'http_requests_total',
            'Total HTTP requests',
            ['route']
        );

        $this->counter->inc([$routeName]);
    }
}
