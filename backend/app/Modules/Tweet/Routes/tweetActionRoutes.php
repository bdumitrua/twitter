<?php

namespace App\Modules\Tweet\Routes;

use App\Modules\Tweet\Controllers\TweetFavoriteController;
use App\Modules\Tweet\Controllers\TweetLikeController;
use Illuminate\Support\Facades\Route;

Route::prefix('tweets/actions')->middleware(['auth:api'])->group(function () {
    // Лайки
    Route::prefix('likes')->controller(TweetLikeController::class)->group(function () {
        // Лайкнуть
        Route::post('{tweet}', 'add')->name('likeTweet');
        // Убрать лайк
        Route::delete('{tweet}', 'remove')->name('dislikeTweet');
    });

    // Избранное (т.е. закладки)
    Route::prefix('favorites')->controller(TweetFavoriteController::class)->group(function () {
        // Добавить в избранное
        Route::post('{tweet}', 'add')->name('addTweetToBookmarks');
        // Удалить из избранного
        Route::delete('{tweet}', 'remove')->name('removeTweetFromBookmarks');
    });
});
