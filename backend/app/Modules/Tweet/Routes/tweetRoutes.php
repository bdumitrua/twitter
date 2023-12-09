<?php

namespace App\Modules\Tweet\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Tweet\Controllers\TweetController;

Route::prefix('tweets')->controller(TweetController::class)->group(function () {
    // Получить по id твита
    Route::get('show/{tweet}', 'show')->name('get_tweet_by_id');

    Route::prefix('user')->group(function () {
        // Получить твиты пользователя
        Route::get('{user}', 'user')->name('get_user_tweets');
        // Получить ответы пользователя
        Route::get('replies/{user}', 'replies')->name('get_user_replies');
        // Получить лайкнутые твиты пользователя
        Route::get('likes/{user}', 'likes')->name('get_user_likes');
        // Получить медиа пользователя
        // ! DOESN'T WORK
        Route::get('media/{user}', 'media')->name('get_user_tweets_with_media');
    });

    Route::middleware(['auth:api'])->group(function () {
        // Получить ленту твитов
        Route::get('feed', 'feed')->name('get_user_feed');
        // Получить твиты списка
        Route::get('list/{usersList}', 'list')->name('get_users_list_tweets');
        // Создать твит
        Route::post('create', 'create')->name('create_tweet');
        // Создать тред
        Route::post('thread', 'thread')->name('create_thread');
        // Удалить твит
        Route::middleware(['checkRights:tweet'])->group(function () {
            Route::delete('destroy/{tweet}', 'destroy')->name('destroy_tweet');
        });
    });
});
