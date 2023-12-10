<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserSubscribtionController;

// Работа с подписками
Route::prefix('users')->middleware(['auth:api'])->controller(UserSubscribtionController::class)->group(function () {
    // На кого пользователь подписан
    Route::get('subscribtions/{user}', 'subscribtions')->name('get_user_subscribtions');
    // Кто подписан на пользователя
    Route::get('subscribers/{user}', 'subscribers')->name('get_user_subscribers');

    Route::prefix('subscribtions')->middleware(['preventSA'])->group(function () {
        // Подписаться на пользователя
        Route::post('{user}', 'add')->name('subscribe_on_user');
        // Отписаться от пользователя
        Route::delete('{user}', 'remove')->name('unsubscribe_from_user');
    });
});
