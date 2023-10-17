<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;

Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::get('/search', 'search')->name('user.search');

    Route::middleware(['auth:api'])->group(function () {
        Route::delete('delete/{user}', 'destroy')->name('user.delete');
    });
});
