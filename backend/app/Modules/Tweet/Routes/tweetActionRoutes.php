<?php

namespace App\Modules\Tweet\Routes;

use App\Modules\Tweet\Controllers\TweetActionController;
use App\Modules\Tweet\Controllers\TweetFavoriteController;
use App\Modules\Tweet\Controllers\TweetLikeController;
use Illuminate\Support\Facades\Route;

Route::prefix('tweets/actions')->middleware(['api.auth'])->group(function () {
    // Лайки
    Route::prefix('likes')->controller(TweetLikeController::class)->group(function () {
        // Получить свои лайки
        Route::get('/', 'index');
        // Лайкнуть
        Route::post('add/{tweet}', 'add');
        // Убрать лайк
        Route::post('remove/{tweet}', 'remove');
    });

    // Избранное (т.е. закладки)
    Route::prefix('favorites')->controller(TweetFavoriteController::class)->group(function () {
        // Получить свои избранные
        Route::get('/', 'index');
        // Добавить в избранное
        Route::post('add/{tweet}', 'add');
        // Удалить из избранного
        Route::post('remove/{tweet}', 'remove');
    });
});
