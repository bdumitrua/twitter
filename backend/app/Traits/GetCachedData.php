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

        if ($updateCache || !Cache::has($cacheKey)) {
            $data = $callback();

            $seconds
                ? Cache::put($cacheKey, $data, TimeHelper::getSeconds($seconds))
                : Cache::forever($cacheKey, $data);

            $prometheusService->incrementCacheMiss($cacheKey);
            return $data;
        }

        $prometheusService->incrementCacheHit($cacheKey);
        return Cache::get($cacheKey);
    }
}
