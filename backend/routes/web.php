<?php

use App\Mail\RegistrationCodeMail;
use Illuminate\Support\Facades\Mail;
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

Route::get('/email', function () {
    Mail::to('fake@email.com')->send(new RegistrationCodeMail());
});

Route::get('/metrics', function () {
    $result = app(PrometheusService::class)->getMetrics();
    return response($result)->header('Content-Type', RenderTextFormat::MIME_TYPE);
});

Route::get('/metrics/wipe', function () {
    app(PrometheusService::class)->clearMetrics();
});
