<?php

namespace App\Modules\Notification\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\DeviceTokenController;

Route::prefix('notifications/tokens')->middleware(['auth:api'])->controller(DeviceTokenController::class)->group(function () {
    // Получить девайс токены авторизованного пользователя
    Route::get('/', 'index')->name('getAuthorizedUserDeviceTokens');
    // Создать девайс токен для пользователя
    Route::post('/', 'create')->name('createNewDeviceToken');

    Route::middleware(['checkRights:deviceToken'])->group(function () {
        // Изменить девайс токен пользователя
        Route::patch('{deviceToken}', 'update')->name('updateDeviceToken');
        // Удалить девайс токен пользователя
        Route::delete('{deviceToken}', 'delete')->name('deleteDeviceToken');
    });
});
