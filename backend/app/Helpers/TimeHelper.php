<?php

namespace App\Helpers;

class TimeHelper
{
    public static function getSeconds(int $seconds)
    {
        return now()->addSeconds($seconds);
    }

    public static function getMinutes(int $minutes)
    {
        return now()->addMinutes($minutes);
    }

    public static function getHours(int $hours)
    {
        return now()->addHours($hours);
    }
}
