<?php

use App\Prometheus\PrometheusServiceProxy;
use Illuminate\Support\Facades\Route;
use Prometheus\RenderTextFormat;

Route::get('/metrics', function () {
    $result = app(PrometheusServiceProxy::class)->getMetrics();
    return response($result)->header('Content-Type', RenderTextFormat::MIME_TYPE);
});

Route::get('/metrics/wipe', function () {
    app(PrometheusServiceProxy::class)->clearMetrics();
});
