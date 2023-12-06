<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserSubscribtionController;

// Работа с подписками
Route::prefix('users/subscribtions')->middleware(['auth:api'])->controller(UserSubscribtionController::class)->group(function () {
    // На кого пользователь подписан
    Route::get('/{user}', 'subscribtions')->name('get_user_subscribtions');
    // Кто подписан на пользователя
    Route::get('subscribers/{user}', 'subscribers')->name('get_user_subscribers');

    Route::middleware(['preventSA'])->group(function () {
        // Подписаться на пользователя
        Route::post('add/{user}', 'add')->name('subscribe_on_user');
        // Отписаться от пользователя
        Route::post('remove/{user}', 'remove')->name('unsubscribe_from_user');
    });
});
