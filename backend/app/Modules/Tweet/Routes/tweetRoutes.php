<?php

namespace App\Modules\Tweet\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Tweet\Controllers\TweetController;

Route::prefix('tweets')->controller(TweetController::class)->group(function () {
    // Получить по id твита
    Route::get('show/{tweet}', 'show');
    // Получить твиты списка
    Route::get('list/{usersList}', 'list');

    Route::prefix('user')->group(function () {
        // Получить твиты пользователя
        Route::get('{user}', 'user');
        // Получить ответы пользователя
        Route::get('replies/{user}', 'replies');
        // Получить лайкнутые твиты пользователя
        Route::get('likes/{user}', 'likes');

        // Получить медиа пользователя
        // ! DOESN'T WORK
        Route::get('media/{user}', 'media');
    });

    Route::middleware(['api.auth'])->group(function () {
        // Получить ленту твитов
        Route::get('feed', 'feed');
        // Создать твит
        Route::post('create', 'create');
        // Создать тред
        Route::post('thread', 'thread');
        // Удалить твит
        Route::middleware(['checkRights:tweet'])->group(function () {
            Route::delete('destroy/{tweet}', 'destroy');
        });
    });
});
