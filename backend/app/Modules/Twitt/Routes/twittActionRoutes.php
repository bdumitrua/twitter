<?php

namespace App\Modules\Twitt\Routes;

use App\Modules\Twitt\Controllers\TwittActionController;
use App\Modules\Twitt\Controllers\TwittFavoriteController;
use App\Modules\Twitt\Controllers\TwittLikeController;
use Illuminate\Support\Facades\Route;

Route::prefix('twitts/actions')->middleware(['auth:api'])->group(function () {
    // Лайки
    Route::prefix('likes')->controller(TwittLikeController::class)->group(function () {
        // Получить свои лайки
        Route::get('/', 'index');
        // Лайкнуть
        Route::post('add/{twitt}', 'add');
        // Убрать лайк
        Route::post('remove/{twitt}', 'remove');
    });

    // Избранное (т.е. закладки)
    Route::prefix('favorites')->controller(TwittFavoriteController::class)->group(function () {
        // Получить свои избранные
        Route::get('/', 'index');
        // Добавить в избранное
        Route::post('add/{twitt}', 'add');
        // Удалить из избранного
        Route::post('remove/{twitt}', 'remove');
    });
});
