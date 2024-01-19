<?php

namespace App\Prometheus;

interface IPrometheusService
{
    public function getMetrics(): string;

    public function clearMetrics(): void;

    public function addResponseTimeHistogram($duration, string $routeName): void;

    public function incrementRequestCounter($routeName): void;

    public function incrementErrorCounter($statusCode, $routeName): void;

    public function incrementCacheHit($cacheKey): void;

    public function incrementCacheMiss($cacheKey): void;

    public function incrementDatabaseQueryCount($source): void;

    public function addDatabaseQueryTimeHistogram($duration, $source): void;

    public function incrementEntityCreatedCount($entityName): void;
}
