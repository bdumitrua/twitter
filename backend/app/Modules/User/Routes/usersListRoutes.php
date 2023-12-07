<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UsersListController;

Route::prefix('users/lists')->middleware(['auth:api'])->controller(UsersListController::class)->group(function () {
    // Получить свои списки
    Route::get('/', 'index')->name('get_authorized_user_users_lists');
    // Посмотреть список
    Route::get('show/{usersList}', 'show')->name('show_users_list');
    // Создать список
    Route::post('create', 'create')->name('create_users_list');

    Route::middleware(['checkRights:usersList'])->group(function () {
        // Изменить список
        Route::patch('update/{usersList}', 'update')->name('update_users_list');
        // Удалить список
        Route::delete('destroy/{usersList}', 'destroy')->name('delete_users_list');

        // Добавить пользователя в список читаемых в списке
        Route::post('members/add/{usersList}/{user}', 'add')->name('add_member_to_users_list');
        // Убрать пользователя из списка читаемых в списке
        Route::post('members/remove/{usersList}/{user}', 'remove')->name('remove_member_from_users_list');
    });

    // Подписаться на список
    Route::post('subscribe/{usersList}', 'subscribe')->name('subscribe_to_users_list');
    // Отписаться от списка
    Route::post('unsubscribe/{usersList}', 'unsubscribe')->name('unsubscribe_from_users_list');
});
