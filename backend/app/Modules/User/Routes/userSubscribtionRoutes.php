<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserSubscribtionController;

// Работа с подписками
Route::prefix('users/subscriptions')->middleware(['auth:api'])->controller(UserSubscribtionController::class)->group(function () {
    // На кого пользователь подписан
    Route::get('/{user}', 'subscriptions');
    // Кто подписан на пользователя
    Route::get('subscribers/{user}', 'subscribers');
    // Подписаться на пользователя
    Route::post('add/{user}', 'add');
    // Отписаться от пользователя
    Route::post('remove/{user}', 'remove');
});
