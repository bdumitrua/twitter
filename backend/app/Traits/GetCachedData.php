<?php

namespace App\Traits;

use App\Helpers\FileGeneratorHelper;
use App\Helpers\TimeHelper;
use Illuminate\Support\Facades\Cache;

trait GetCachedData
{
    protected function getCachedData(string $cacheKey, int $seconds, \Closure $callback, bool $updateCache = false)
    {
        if ($updateCache) {
            $data = $callback();

            Cache::put($cacheKey, $data, TimeHelper::getSeconds($seconds));
            return $data;
        }

        return Cache::remember($cacheKey, TimeHelper::getSeconds($seconds), $callback);
    }
}
