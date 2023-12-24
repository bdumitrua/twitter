<?php

use App\Modules\User\Controllers\UserGroupController;
use Illuminate\Support\Facades\Route;


Route::prefix('users/groups')->middleware(['auth:api'])->controller(UserGroupController::class)->group(function () {
    // Получить мои группы 
    Route::get('/', 'index')->name('getUserGroups');
    // Создать группу
    Route::post('/', 'create')->name('createUserGroup');

    Route::middleware(['checkRights:userGroup'])->group(function () {
        // Получить группу по id 
        Route::get('{userGroup}', 'show')->name('showUserGroup');
        // Изменить данные группы
        Route::patch('{userGroup}', 'update')->name('updateUserGroup');
        // Удалить группу
        Route::delete('{userGroup}', 'delete')->name('deleteUserGroup');

        // Добавить пользователя в группу
        Route::post('/members/{userGroup}/{user}', 'add')->name('addUserToUserGroup');
        // Удалить пользователя из группы
        Route::delete('/members/{userGroup}/{user}', 'remove')->name('removeUserFromUserGroup');
    });
});
