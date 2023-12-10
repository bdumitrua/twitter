<?php

namespace App\Modules\Tweet\Routes;

use App\Modules\Tweet\Controllers\TweetFavoriteController;
use App\Modules\Tweet\Controllers\TweetLikeController;
use Illuminate\Support\Facades\Route;

Route::prefix('tweets/actions')->middleware(['auth:api'])->group(function () {
    // Лайки
    Route::prefix('likes')->controller(TweetLikeController::class)->group(function () {
        // Лайкнуть
        Route::post('{tweet}', 'add')->name('like_tweet');
        // Убрать лайк
        Route::delete('{tweet}', 'remove')->name('dislike_tweet');
    });

    // Избранное (т.е. закладки)
    Route::prefix('favorites')->controller(TweetFavoriteController::class)->group(function () {
        // Добавить в избранное
        Route::post('{tweet}', 'add')->name('add_tweet_to_bookmarks');
        // Удалить из избранного
        Route::delete('{tweet}', 'remove')->name('remove_tweet_from_bookmarks');
    });
});
