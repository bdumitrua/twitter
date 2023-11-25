<?php

namespace App\Modules\Auth\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    // Начать регистрацию
    Route::post('start', 'start');
    // Зарегистрироваться
    Route::post('confirm/{authRegistration}', 'confirm');
    // Зарегистрироваться
    Route::post('register/{authRegistration}', 'register');
    // Залогиниться
    Route::post('login', 'login')->name('auth.login');
    // Получить новый токен (по уже истёкшему)
    Route::get('refresh', 'refresh');

    Route::middleware(['auth:api'])->group(function () {
        // Выйти
        Route::post('logout', 'logout');
    });
});
