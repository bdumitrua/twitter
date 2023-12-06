<?php

namespace App\Modules\Notification\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\DeviceTokenController;

Route::prefix('notifications/tokens')->middleware(['auth:api'])->controller(DeviceTokenController::class)->group(function () {
    // Получить девайс токены авторизованного пользователя
    Route::get('/', 'index');
    // Создать девайс токен для пользователя
    Route::post('create', 'create');

    Route::middleware(['checkRights:deviceToken'])->group(function () {
        // Изменить девайс токен пользователя
        Route::patch('update/{deviceToken}', 'update');
        // Удалить девайс токен пользователя
        Route::delete('delete/{deviceToken}', 'delete');
    });
});
