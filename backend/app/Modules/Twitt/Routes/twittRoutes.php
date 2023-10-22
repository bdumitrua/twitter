<?php

namespace App\Modules\Twitt\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Twitt\Controllers\TwittController;

Route::prefix('twitts')->controller(TwittController::class)->group(function () {
    // Получить по id твита
    Route::get('show/{twitt}', 'show');
    // Получить твиты пользователя
    Route::get('user/{user}', 'user');
    // Получить твиты списка
    Route::get('list/{usersList}', 'list');

    Route::middleware(['auth:api'])->group(function () {
        // Получить ленту твитов
        Route::get('/', 'index');
        // Создать твит
        Route::post('create', 'create');
        // Удалить твит
        Route::middleware(['checkRights:twitt'])->group(function () {
            Route::delete('destroy/{twitt}', 'destroy');
        });
    });
});
