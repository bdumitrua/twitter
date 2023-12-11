<?php

namespace App\Modules\Tweet\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Tweet\Controllers\TweetController;

Route::prefix('tweets')->controller(TweetController::class)->group(function () {
    // Получить по id твита
    Route::get('show/{tweet}', 'show')->name('get_tweet_by_id');

    Route::middleware(['auth:api'])->group(function () {
        // Получить ленту твитов
        Route::get('feed', 'feed')->name('get_user_feed');
        // Получить твиты, которые авторизованный юзер добавил в избранное
        Route::get('bookmarks', 'bookmarks')->name('get_authorized_user_bookmarks');
        // Получить твиты списка
        Route::get('list/{usersList}', 'list')->name('get_users_list_tweets');
        // Создать твит
        Route::post('create', 'create')->name('create_tweet');
        // Создать тред
        Route::post('thread', 'thread')->name('create_thread');
        // Удалить репост
        Route::post('unrepost/{tweet}', 'unrepost')->name('unrepost_tweet');
        Route::middleware(['checkRights:tweet'])->group(function () {
            // Удалить твит
            Route::delete('{tweet}', 'destroy')->name('destroy_tweet');
        });
    });

    Route::prefix('user')->group(function () {
        // Получить твиты пользователя
        Route::get('{user}', 'user')->name('get_user_tweets');
        // Получить ответы пользователя
        Route::get('{user}/replies', 'replies')->name('get_user_replies');
        // Получить лайкнутые твиты пользователя
        Route::get('{user}/likes', 'likes')->name('get_user_likes');
        // Получить медиа пользователя
        // ! DOESN'T WORK
        Route::get('{user}/media', 'media')->name('get_user_tweets_with_media');
    });
});
