<?php

use App\Prometheus\PrometheusService;
use Illuminate\Support\Facades\Route;
use Prometheus\RenderTextFormat;

Route::get('/metrics', function () {
    $result = app(PrometheusService::class)->getMetrics();
    return response($result)->header('Content-Type', RenderTextFormat::MIME_TYPE);
});

Route::get('/metrics/wipe', function () {
    app(PrometheusService::class)->clearMetrics();
});
