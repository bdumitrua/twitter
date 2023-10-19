<?php

namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;

Route::prefix('twitts/lists')->middleware(['auth:api'])->controller(Controller::class)->group(function () {
    // Получить свои списки
    Route::get('/', 'index');
    // Создать список
    Route::post('create', 'create');

    Route::middleware(['checkRights:userslist'])->group(function () {
        // Изменить список
        Route::patch('update/{userslist}', 'update');
        // Удалить список
        Route::delete('destroy/{userslist}', 'destroy');

        // Добавить пользователя в список читаемых в списке
        Route::post('members/add/{userslist}/{user}', 'add');
        // Убрать пользователя из списка читаемых в списке
        Route::post('members/remove/{userslist}/{user}', 'remove');
    });

    // Подписаться на список
    Route::post('subscribe/{userslist}', 'add');
    // Отписаться от списка
    Route::post('unsubscribe/{userslist}', 'remove');
});
