<?php

use App\Modules\User\Controllers\UserGroupController;
use Illuminate\Support\Facades\Route;


Route::prefix('users/groups')->middleware(['auth:api'])->controller(UserGroupController::class)->group(function () {
    // Получить мои группы 
    Route::get('/', 'index')->name('get_user_groups');
    // Создать группу
    Route::post('/', 'create')->name('create_user_group');

    Route::middleware(['checkRights:userGroup'])->group(function () {
        // Получить мои группы 
        Route::get('{userGroup}', 'show')->name('show_user_group');
        // Изменить данные группы
        Route::patch('{userGroup}', 'update')->name('update_user_group');
        // Удалить группу
        Route::delete('{userGroup}', 'destroy')->name('destroy_user_group');

        // Добавить пользователя в группу
        Route::post('add/{userGroup}/{user}', 'add')->name('add_user_to_user_group');
        // Удалить пользователя из группы
        Route::post('remove/{userGroup}/{user}', 'remove')->name('remove_user_from_user_group');;
    });
});
