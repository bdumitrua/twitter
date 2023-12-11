<?php

namespace App\Modules\Notification\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\NotificationController;

Route::prefix('notifications')->middleware(['auth:api'])->controller(NotificationController::class)->group(function () {
    // Получить свои уведомления
    Route::get('/', 'index')->name('getAuthorizedUserNotifications');

    Route::middleware(['checkRights:notification'])->group(function () {
        // Изменить статус
        Route::patch('{notification}', 'update')->name('updateNotification');
    });
});
