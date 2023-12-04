<?php

namespace App\Modules\Auth\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::prefix('registration')->group(function () {
        // Начать регистрацию
        Route::post('start', 'registrationStart');
        // Подтвердить регистрацию кодом
        Route::post('confirm/{authRegistration}', 'registrationConfirm');
        // Зарегистрироваться
        Route::post('end/{authRegistration}', 'registrationEnd');
    });

    Route::prefix('reset')->group(function () {
        // Проверить существование аккаунта по почте
        Route::get('check', 'resetCheck');
        // Подтвердить сборс пароля кодом
        Route::post('confirm/{authReset}', 'resetConfirm');
        // Изменить пароль аккаунта
        Route::post('end/{authReset}', 'resetEnd');
    });

    // Залогиниться
    Route::post('login', 'login')->name('auth.login');
    // Получить новый токен (по уже истёкшему)
    Route::get('refresh', 'refresh');

    Route::middleware(['api.auth'])->group(function () {
        // Выйти
        Route::post('logout', 'logout');
    });
});
