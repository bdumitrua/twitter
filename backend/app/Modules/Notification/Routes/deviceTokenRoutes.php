<?php

namespace App\Modules\Notification\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Controllers\NotificationController;

Route::prefix('notifications/tokens')->middleware(['auth:api'])->controller(NotificationController::class)->group(function () {
    Route::middleware(['checkRights:notification'])->group(function () {
        // 
    });
});
