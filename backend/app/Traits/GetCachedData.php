<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait GetCachedData
{
    private function getCachedData(string $key, callable $callback, int $minutes = 1)
    {
        if ($cachedData = Cache::get($key)) {
            return $cachedData;
        }

        $data = $callback();
        Cache::put($key, $data, now()->addMinutes($minutes));

        return $data;
    }
}
