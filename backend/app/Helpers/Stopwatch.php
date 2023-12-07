<?php

namespace App\Helpers;

class Stopwatch
{
    private float $startTime;

    public function start(): void
    {
        $this->startTime = microtime(true);
    }

    public function stop(): float
    {
        return microtime(true) - $this->startTime;
    }
}
