<?php

namespace App\Modules\Notification\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\NotificationController;

Route::prefix('notifications')->middleware(['auth:api'])->controller(NotificationController::class)->group(function () {
    // Получить свои уведомления
    Route::get('/', 'index')->name('getAuthorizedUserNotifications');
    // Изменить статус на прочитано
    Route::patch('{notificationUuid}', 'read')->name('readNotification');
    // Удалить уведомление
    Route::delete('{notificationUuid}', 'delete')->name('deleteNotification');
});
