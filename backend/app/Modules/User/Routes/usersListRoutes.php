<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UsersListController;

Route::prefix('users/lists')->middleware(['auth:api'])->controller(UsersListController::class)->group(function () {
    // Получить свои списки
    Route::get('/', 'index');
    // Посмотреть список
    Route::get('show/{usersList}', 'show');
    // Создать список
    Route::post('create', 'create');

    Route::middleware(['checkRights:usersList'])->group(function () {
        // Изменить список
        Route::patch('update/{usersList}', 'update');
        // Удалить список
        Route::delete('destroy/{usersList}', 'destroy');

        // Добавить пользователя в список читаемых в списке
        Route::post('members/add/{usersList}/{user}', 'add');
        // Убрать пользователя из списка читаемых в списке
        Route::post('members/remove/{usersList}/{user}', 'remove');
    });

    // Подписаться на список
    Route::post('subscribe/{usersList}', 'subscribe');
    // Отписаться от списка
    Route::post('unsubscribe/{usersList}', 'unsubscribe');
});
