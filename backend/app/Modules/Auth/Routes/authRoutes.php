<?php

namespace App\Modules\Auth\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::prefix('registration')->group(function () {
        // Начать регистрацию
        Route::post('start', 'registrationStart')->name('start_registration');
        // Подтвердить регистрацию кодом
        Route::post('confirm/{authRegistration}', 'registrationConfirm')->name('confirm_registration_code');
        // Зарегистрироваться
        Route::post('end/{authRegistration}', 'registrationEnd')->name('end_registration');
    });

    Route::prefix('reset')->group(function () {
        // Проверить существование аккаунта по почте
        Route::get('check', 'resetCheck')->name('check_email_for_password_reset');
        // Подтвердить сборс пароля кодом
        Route::post('confirm/{authReset}', 'resetConfirm')->name('confirm_password_reset_code');
        // Изменить пароль аккаунта
        Route::post('end/{authReset}', 'resetEnd')->name('end_password_reseting');
    });

    // Залогиниться
    Route::post('login', 'login')->name('auth_login');
    // Получить новый токен (по уже истёкшему)
    Route::get('refresh', 'refresh')->name('auth_refresh_token');
    Route::middleware(['auth:api'])->group(function () {
        // Выйти
        Route::post('logout', 'logout')->name('auth_logout');
    });
});
