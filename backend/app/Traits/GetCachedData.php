<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait GetCachedData
{
    private function getCachedData(string $key, callable $callback, ?int $seconds = 60)
    {
        if ($cachedData = Cache::get($key)) {
            return $cachedData;
        }

        $data = $callback();

        if (!empty($seconds)) {
            Cache::put($key, $data, now()->addSeconds($seconds));
        } else {
            Cache::forever($key, $data);
        }

        return $data;
    }
}
