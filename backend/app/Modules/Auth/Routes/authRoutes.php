<?php

namespace App\Modules\Auth\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::prefix('registration')->group(function () {
        // Начать регистрацию
        Route::post('start', 'registrationStart')->name('startRegistration');
        // Подтвердить регистрацию кодом
        Route::post('confirm/{authRegistration}', 'registrationConfirm')->name('confirmRegistrationCode');
        // Зарегистрироваться
        Route::post('end/{authRegistration}', 'registrationEnd')->name('endRegistration');
    });

    Route::prefix('reset')->group(function () {
        // Проверить существование аккаунта по почте
        Route::get('check', 'resetCheck')->name('checkEmailForPasswordReset');
        // Подтвердить сборс пароля кодом
        Route::post('confirm/{authReset}', 'resetConfirm')->name('confirmPasswordResetCode');
        // Изменить пароль аккаунта
        Route::post('end/{authReset}', 'resetEnd')->name('endPasswordReset');
    });

    // Залогиниться
    Route::post('login', 'login')->name('authLogin');
    // Получить новый токен (по уже истёкшему)
    Route::get('refresh', 'refresh')->name('authRefreshToken');
    Route::middleware(['auth:api'])->group(function () {
        // Выйти
        Route::post('logout', 'logout')->name('authLogout');
    });
});
