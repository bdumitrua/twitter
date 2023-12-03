<?php

namespace App\Modules\Notification\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\NotificationController;

Route::prefix('notifications')->middleware(['api.auth'])->controller(NotificationController::class)->group(function () {
    // Получить свои уведомления
    Route::get('/', 'index');

    Route::middleware(['checkRights:notification'])->group(function () {
        // Изменить статус
        Route::patch('update/{notification}', 'update');
    });
});
