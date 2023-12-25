<?php

namespace App\Modules\Notification\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\NotificationsSubscribtionController;

Route::prefix('notifications/subscribtions')->middleware(['auth:api', 'preventSA'])->controller(NotificationsSubscribtionController::class)->group(function () {
    // Подписаться на уведомления другого пользователя
    Route::post('/{user}', 'subscribe')->name('subscribeOnUserNotification');
    // Отписаться от уведомлений другого пользователя
    Route::delete('/{user}', 'unsubscribe')->name('unsubscribeFromUserNotification');
});
