<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Modules\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// @See app/Providers/RouteServiceProvider.php
// checkRights:userGroup

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    // Зарегистрироваться
    Route::post('register', 'register');
    // Залогиниться
    Route::post('login', 'login');
    // Получить новый токен (по уже истёкшему)
    Route::get('refresh', 'refresh');

    Route::middleware(['auth:api'])->group(function () {
        // Выйти
        Route::post('logout', 'logout');
    });
});


// Работа с твитами
Route::prefix('twitts')->controller(Controller::class)->group(function () {
    // Получить по id твита
    Route::get('show/{twitt}', 'show');
    // Получить твиты пользователя
    Route::get('user/{user}', 'show');
    // Получить твиты списка
    Route::get('list/{userslist}', 'show');

    Route::middleware(['auth:api'])->group(function () {
        // Создать твит
        Route::post('create', 'create');
        // Удалить твит
        Route::middleware(['checkRights:twitt'])->group(function () {
            Route::delete('destroy/{twitt}', 'destroy');
        });
    });
});

// Действия производимые с твитами
Route::prefix('twitts/actions')->middleware(['auth:api'])->controller(Controller::class)->group(function () {
    // Лайки
    Route::prefix('likes')->group(function () {
        // Получить свои лайки
        Route::get('/', 'index');
        // Лайкнуть
        Route::post('add/{twitt}', 'add');
        // Убрать лайк
        Route::post('remove/{twitt}', 'remove');
    });

    // Избранное (т.е. закладки)
    Route::prefix('favorites')->group(function () {
        // Получить свои избранные
        Route::get('/', 'index');
        // Добавить в избранное
        Route::post('add/{twitt}', 'add');
        // Удалить из избранного
        Route::post('remove/{twitt}', 'remove');
    });
});

// Опросы
Route::prefix('polls')->middleware(['auth:api'])->controller(Controller::class)->group(function () {
    // Создать опрос
    Route::post('create', 'create');
    // Проголосовать в опросе
    Route::post('vote/add/{poll}', 'add');
    // Убрать голос в опросе
    Route::post('vote/remove/{poll}', 'remove');
});

// Списки с только с постами выбранных пользователей
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
