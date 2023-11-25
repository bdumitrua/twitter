<?php

namespace App\Modules\Tweet\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Tweet\Controllers\TweetController;

Route::prefix('tweets')->controller(TweetController::class)->group(function () {
    // Получить по id твита
    Route::get('show/{tweet}', 'show');
    // Получить твиты пользователя
    Route::get('user/{user}', 'user');
    // Получить твиты списка
    Route::get('list/{usersList}', 'list');

    Route::middleware(['auth:api'])->group(function () {
        // Получить ленту твитов
        Route::get('feed', 'feed');
        // Создать твит
        Route::post('create', 'create');
        // Удалить твит
        Route::middleware(['checkRights:tweet'])->group(function () {
            Route::delete('destroy/{tweet}', 'destroy');
        });
    });
});
