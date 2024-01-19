<?php

namespace App\Prometheus;

class PrometheusServiceProxy implements IPrometheusService
{
    private $prometheusService;
    private $isActive;

    public function __construct(
        PrometheusService $prometheusService
    ) {
        $this->prometheusService = $prometheusService;
        $this->isActive = !app()->environment('testing');
    }

    public function getMetrics(): string
    {
        return $this->isActive ? $this->prometheusService->getMetrics() : '';
    }

    public function clearMetrics(): void
    {
        if ($this->isActive) {
            $this->prometheusService->clearMetrics();
        }
    }

    public function addResponseTimeHistogram($duration, string $routeName): void
    {
        if ($this->isActive) {
            $this->prometheusService->addResponseTimeHistogram($duration, $routeName);
        }
    }

    public function incrementRequestCounter($routeName): void
    {
        if ($this->isActive) {
            $this->prometheusService->incrementRequestCounter($routeName);
        }
    }

    public function incrementErrorCounter($statusCode, $routeName): void
    {
        if ($this->isActive) {
            $this->prometheusService->incrementErrorCounter($statusCode, $routeName);
        }
    }

    public function incrementCacheHit($cacheKey): void
    {
        if ($this->isActive) {
            $this->prometheusService->incrementCacheHit($cacheKey);
        }
    }

    public function incrementCacheMiss($cacheKey): void
    {
        if ($this->isActive) {
            $this->prometheusService->incrementCacheMiss($cacheKey);
        }
    }

    public function incrementDatabaseQueryCount($source): void
    {
        if ($this->isActive) {
            $this->prometheusService->incrementDatabaseQueryCount($source);
        }
    }

    public function addDatabaseQueryTimeHistogram($duration, $source): void
    {
        if ($this->isActive) {
            $this->prometheusService->addDatabaseQueryTimeHistogram($duration, $source);
        }
    }

    public function incrementEntityCreatedCount($entityName): void
    {
        if ($this->isActive) {
            $this->prometheusService->incrementEntityCreatedCount($entityName);
        }
    }
}
