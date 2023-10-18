<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;


Route::prefix('users')->controller(UserController::class)->group(function () {
    // Получить данные по id пользователя
    Route::get('/show/{user}', 'show');

    Route::middleware(['auth:api'])->group(function () {
        // Получить данные своего аккаунта
        Route::get('/', 'index');
        // Поиск по нику/ссылке пользователя
        Route::get('/search', 'search');
    });
});
