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

// * @See app/Providers/RouteServiceProvider.php

// Опросы
Route::prefix('polls')->middleware(['auth:api'])->controller(Controller::class)->group(function () {
    // Создать опрос
    Route::post('create', 'create');
    // Проголосовать в опросе
    Route::post('vote/add/{poll}', 'add');
    // Убрать голос в опросе
    Route::post('vote/remove/{poll}', 'remove');
});
