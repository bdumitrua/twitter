<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;


Route::prefix('users')->controller(UserController::class)->group(function () {
    // Получить данные по id пользователя
    Route::get('{user}', 'show')->name('showUser');

    Route::middleware(['auth:api'])->group(function () {
        // Получить данные своего аккаунта
        Route::get('/', 'index')->name('getAuthorizedUserData');
        // Изменить данные аккаунта
        Route::patch('/', 'update')->name('updateUserData');
    });
});
