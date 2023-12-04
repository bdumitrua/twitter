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

Route::get('/', function () {
    // 
});

Route::get('/metrics', function () {
    \Prometheus\Storage\Redis::setDefaultOptions(
        [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', '6379'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'timeout' => 0.1, // in seconds
            'read_timeout' => '10', // in seconds
            'persistent_connections' => false
        ]
    );

    $registry = CollectorRegistry::getDefault();
    $renderer = new RenderTextFormat();
    $result = $renderer->render($registry->getMetricFamilySamples());

    return response($result)->header('Content-Type', RenderTextFormat::MIME_TYPE);
});

Route::get('/test-metric', function () {
    \Prometheus\Storage\Redis::setDefaultOptions(
        [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'port' => env('REDIS_PORT', '6379'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'timeout' => 0.1, // in seconds
            'read_timeout' => '10', // in seconds
            'persistent_connections' => false
        ]
    );

    $counter = CollectorRegistry::getDefault()->getOrRegisterCounter('twitter', 'test_metric_counter', 'Counter for test metric');
    $counter->inc();

    return "Test metric incremented";
});
