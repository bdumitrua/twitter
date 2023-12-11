<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UsersListController;

Route::prefix('users/lists')->middleware(['auth:api'])->controller(UsersListController::class)->group(function () {
    // Получить свои списки
    Route::get('/', 'index')->name('getAuthorizedUserUsersLists');
    // Посмотреть список
    Route::get('{usersList}', 'show')->name('showUsersList');
    // Создать список
    Route::post('/', 'create')->name('createUsersList');

    Route::middleware(['checkRights:usersList'])->group(function () {
        // Изменить список
        Route::patch('{usersList}', 'update')->name('updateUsersList');
        // Удалить список
        Route::delete('{usersList}', 'destroy')->name('deleteUsersList');

        // Добавить пользователя в список читаемых в списке
        Route::post('members/{usersList}/{user}', 'add')->name('addMemberToUsersList');
        // Убрать пользователя из списка читаемых в списке
        Route::delete('members/{usersList}/{user}', 'remove')->name('removeMemberFromUsersList');
    });

    // Получить участников списка
    Route::get('members/{usersList}', 'members')->name('getUsersListMembers');
    // Получить подписчиков списка
    Route::get('subscribtions/{usersList}', 'subscribtions')->name('getUsersListSubscribers');

    // Подписаться на список
    Route::post('subscribtions/{usersList}', 'subscribe')->name('subscribeToUsersList');
    // Отписаться от списка
    Route::delete('subscribtions/{usersList}', 'unsubscribe')->name('unsubscribeFromUsersList');
});
