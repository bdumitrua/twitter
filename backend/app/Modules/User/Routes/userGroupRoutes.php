<?php

use App\Modules\User\Controllers\UserGroupController;
use Illuminate\Support\Facades\Route;


Route::prefix('users/groups')->middleware(['auth:api'])->controller(UserGroupController::class)->group(function () {
    // Получить мои группы 
    Route::get('/', 'index');
    // Создать группу
    Route::post('create', 'create');
    // Изменить группу
    Route::patch('update/{userGroup}', 'update');
    // Удалить группу
    Route::delete('destroy/{userGroup}', 'destroy');

    // Добавить пользователя в группу
    Route::post('add/{userGroup}/{user}', 'add');
    // Удалить пользователя из группы
    Route::post('remove/{userGroup}/{user}', 'remove');
});
