<?php

namespace App\Traits;

use App\Helpers\FileGeneratorHelper;
use App\Helpers\TimeHelper;
use App\Prometheus\PrometheusService;
use Illuminate\Support\Facades\Cache;

trait GetCachedData
{
    protected function getCachedData(string $cacheKey, ?int $seconds, \Closure $callback, bool $updateCache = false)
    {
        $prometheusService = app(PrometheusService::class);
        $cacheKeyForMetrics = explode(':', $cacheKey)[0];

        if ($updateCache || !Cache::has($cacheKey)) {
            $data = $callback();

            $seconds
                ? Cache::put($cacheKey, $data, TimeHelper::getSeconds($seconds))
                : Cache::forever($cacheKey, $data);

            $prometheusService->incrementCacheMiss($cacheKeyForMetrics);
            return $data;
        }

        $prometheusService->incrementCacheHit($cacheKeyForMetrics);
        return Cache::get($cacheKey);
    }
}
