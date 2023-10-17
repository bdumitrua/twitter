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

// Авторизация
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    // Зарегистрироваться
    Route::post('register', 'register')->name('auth.register');
    // Залогиниться
    Route::post('login', 'login')->name('auth.login');
    // Получить новый токен (по уже истёкшему)
    Route::get('refresh', 'refresh')->name('auth.refresh');

    Route::middleware(['auth:api'])->group(function () {
        // Выйти
        Route::post('logout', 'logout')->name('auth.logout');
    });
});

// Работа с пользователями
Route::prefix('users')->controller(UserController::class)->group(function () {
    // Получить данные по id пользователя
    Route::get('/show/{user}', 'show')->name('user.byid');

    Route::middleware(['auth:api'])->group(function () {
        // Получить данные своего аккаунта
        Route::get('/', 'index')->name('user.index');
        // Поиск по нику/ссылке пользователя
        Route::get('/search', 'search')->name('user.search');
    });
});

// Работа с подписками
Route::prefix('users/subscriptions')->middleware(['auth:api'])->controller(Controller::class)->group(function () {
    // На кого пользователь подписан
    Route::get('/{user}', 'subscriptions');
    // Кто подписан на пользователя
    Route::get('subscribers/{user}', 'subscribers');
    // Подписаться на пользователя
    Route::post('add/{user}', 'add');
    // Отписаться от пользователя
    Route::post('remove/{user}', 'remove');
});

// Работа с группами пользователей
Route::prefix('users/groups')->middleware(['auth:api'])->controller(Controller::class)->group(function () {
    // Получить мои группы 
    Route::get('/', 'index');
    // Создать группу
    Route::post('create', 'create');
    // Изменить группу
    Route::patch('update/{post}', 'update');
    // Удалить группу
    Route::delete('destroy/{post}', 'destroy');

    // Добавить пользователя в группу
    Route::post('add/{user}', 'add');
    // Удалить пользователя из группы
    Route::post('remove/{user}', 'remove');
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
        Route::delete('destroy/{twitt}', 'destroy');
    });
});

// Действия производимые с твитами
Route::prefix('twitts/actions/')->middleware(['auth:api'])->controller(Controller::class)->group(function () {
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
Route::prefix('subscriptions/lists')->middleware(['auth:api'])->controller(Controller::class)->group(function () {
    // Получить свои списки
    Route::get('/', 'index');
    // Создать список
    Route::post('create', 'create');
    // Изменить список
    Route::patch('update/{userslist}', 'update');
    // Удалить список
    Route::delete('destroy/{userslist}', 'destroy');

    // Добавить пользователя в список читаемых в списке
    Route::post('members/add/{user}', 'add');
    // Убрать пользователя из списка читаемых в списке
    Route::post('members/remove/{user}', 'remove');
});
