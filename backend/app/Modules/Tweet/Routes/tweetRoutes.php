<?php

namespace App\Modules\Tweet\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Tweet\Controllers\TweetController;

Route::prefix('tweets')->controller(TweetController::class)->group(function () {
    // Получить по id твита
    Route::get('show/{tweet}', 'show')->name('getTweetById');

    Route::middleware(['auth:api'])->group(function () {
        // Получить ленту твитов
        Route::get('feed', 'feed')->name('getUserFeed');
        // Получить твиты, которые авторизованный юзер добавил в избранное
        Route::get('bookmarks', 'bookmarks')->name('getAuthorizedUserBookmarks');
        // Получить твиты списка
        Route::get('list/{usersList}', 'list')->name('getUsersListTweets');
        // Создать твит
        Route::post('create', 'create')->name('createTweet');
        // Создать тред
        Route::post('thread', 'thread')->name('createThread');
        // Удалить репост
        Route::post('unrepost/{tweet}', 'unrepost')->name('unrepostTweet');
        Route::middleware(['checkRights:tweet'])->group(function () {
            // Удалить твит
            Route::delete('{tweet}', 'destroy')->name('destroyTweet');
        });
    });

    Route::prefix('user')->group(function () {
        // Получить твиты пользователя
        Route::get('{user}', 'user')->name('getUserTweets');
        // Получить ответы пользователя
        Route::get('{user}/replies', 'replies')->name('getUserReplies');
        // Получить лайкнутые твиты пользователя
        Route::get('{user}/likes', 'likes')->name('getUserLikes');
        // Получить медиа пользователя
        // ! DOESN'T WORK
        Route::get('{user}/media', 'media')->name('getUserTweetsWithMedia');
    });
});
