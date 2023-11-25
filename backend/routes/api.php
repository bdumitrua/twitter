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
    // Начать регистрацию
    Route::post('start', 'start');
    // Зарегистрироваться
    Route::post('confirm/{authRegistration}', 'confirm');
    // Зарегистрироваться
    Route::post('register/{authRegistration}', 'register');
    // Залогиниться
    Route::post('login', 'login')->name('auth.login');
    // Получить новый токен (по уже истёкшему)
    Route::get('refresh', 'refresh');

    Route::middleware(['auth:api'])->group(function () {
        // Выйти
        Route::post('logout', 'logout');
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


Route::prefix('kafka')->controller(Controller::class)->group(function () {
    Route::get('create', 'handleKafka');
});
