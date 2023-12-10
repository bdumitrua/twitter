<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UsersListController;

Route::prefix('users/lists')->middleware(['auth:api'])->controller(UsersListController::class)->group(function () {
    // Получить свои списки
    Route::get('/', 'index')->name('get_authorized_user_users_lists');
    // Посмотреть список
    Route::get('{usersList}', 'show')->name('show_users_list');
    // Создать список
    Route::post('/', 'create')->name('create_users_list');

    Route::middleware(['checkRights:usersList'])->group(function () {
        // Изменить список
        Route::patch('{usersList}', 'update')->name('update_users_list');
        // Удалить список
        Route::delete('{usersList}', 'destroy')->name('delete_users_list');

        // Добавить пользователя в список читаемых в списке
        Route::post('members/{usersList}/{user}', 'add')->name('add_member_to_users_list');
        // Убрать пользователя из списка читаемых в списке
        Route::delete('members/{usersList}/{user}', 'remove')->name('remove_member_from_users_list');
    });

    // Подписаться на список
    Route::post('subscribtions/{usersList}', 'subscribe')->name('subscribe_to_users_list');
    // Отписаться от списка
    Route::delete('subscribtions/{usersList}', 'unsubscribe')->name('unsubscribe_from_users_list');
});
