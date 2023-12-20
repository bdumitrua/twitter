<?php

namespace App\Modules\Notification\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\NotificationsSubscribtionController;

Route::prefix('notifications/subscribtions')->middleware(['auth:api'])->controller(NotificationsSubscribtionController::class)->group(function () {
    // Подписаться на уведомления другого пользователя
    Route::post('/{user}', 'subscribe')->name('userSubscribtionOnNotifications');
    // Отписаться от уведомлений другого пользователя
    Route::delete('/{user}', 'unsubscribe')->name('userUnsubscribtionFromNotifications');
});
