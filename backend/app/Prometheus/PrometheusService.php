<?php

namespace App\Prometheus;

use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Exception\MetricNotFoundException;

class PrometheusService
{
    protected $countersNamespace = 'twitter';
    protected $registry;

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
        $histogram = $this->registry->getOrRegisterHistogram(
            $this->countersNamespace,
            'http_response_time_seconds',
            'Duration of HTTP response in seconds',
            ['route']
        );

        $histogram->observe($duration, [$routeName]);
    }

    public function incrementRequestCounter($routeName): void
    {
        $counter = $this->registry->getOrRegisterCounter(
            $this->countersNamespace,
            'http_requests_total',
            'Total HTTP requests',
            ['route']
        );

        $counter->inc([$routeName]);
    }

    public function incrementErrorCounter($statusCode, $routeName)
    {
        $counter = $this->registry->getOrRegisterCounter(
            $this->countersNamespace,
            'http_errors_total',
            'Total HTTP errors',
            ['status_code', 'route']
        );
        $counter->inc([$statusCode, $routeName]);
    }

    public function incrementCacheHit($cacheKey)
    {
        $counter = $this->registry->getOrRegisterCounter(
            $this->countersNamespace,
            'cache_hits',
            'Total cache hits',
            ['cache_key']
        );
        $counter->inc([$cacheKey]);
    }

    public function incrementCacheMiss($cacheKey)
    {
        $counter = $this->registry->getOrRegisterCounter(
            $this->countersNamespace,
            'cache_misses',
            'Total cache misses',
            ['cache_key']
        );
        $counter->inc([$cacheKey]);
    }

    public function incrementDatabaseQueryCount($source)
    {
        $counter = $this->registry->getOrRegisterCounter(
            $this->countersNamespace,
            'database_queries_total',
            'Total database queries',
            ['source']
        );
        $counter->inc([$source]);
    }

    public function addDatabaseQueryTimeHistogram($duration, $source): void
    {
        $histogram = $this->registry->getOrRegisterHistogram(
            $this->countersNamespace,
            'database_queries_time',
            'Duration of database query in seconds',
            ['source']
        );

        $histogram->observe($duration, ['source' => $source]);
    }

    public function incrementEntityCreatedCount($entityName)
    {
        $counter = $this->registry->getOrRegisterCounter(
            $this->countersNamespace,
            'entities_total',
            'Total count of all entities',
            ['entity']
        );
        $counter->inc([$entityName]);
    }
}
