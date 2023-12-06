<?php

use Illuminate\Support\Facades\Route;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/metrics', function () {
    $registry = CollectorRegistry::getDefault();
    $renderer = new RenderTextFormat();
    $result = $renderer->render($registry->getMetricFamilySamples());

    return response($result)->header('Content-Type', RenderTextFormat::MIME_TYPE);
});

Route::get('/metrics/wipe', function () {
    $registry = CollectorRegistry::getDefault();
    $registry->wipeStorage();
});

Route::get('/test-metric', function () {
    $counter = CollectorRegistry::getDefault()->getOrRegisterCounter('twitter', 'test_metric_counter', 'Counter for test metric');
    $counter->inc();
});
