<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;


Route::prefix('users')->controller(UserController::class)->group(function () {
    // Получить данные по id пользователя
    Route::get('show/{user}', 'show')->name('show_user');

    Route::middleware(['auth:api'])->group(function () {
        // Получить данные своего аккаунта
        Route::get('/', 'index')->name('get_authorized_user_data');
        // Поиск по нику/ссылке пользователя
        Route::patch('update', 'update')->name('update_user_data');
    });
});
