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
    Route::post('login', 'login')->name('auth.login');
    // Получить новый токен (по уже истёкшему)
    Route::get('refresh', 'refresh');

    Route::middleware(['auth:api'])->group(function () {
        // Выйти
        Route::post('logout', 'logout');
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
