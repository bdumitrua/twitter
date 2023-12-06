<?php

namespace App\Modules\Search\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Search\Controllers\SearchController;

Route::prefix('search')->middleware(['auth:api'])->controller(SearchController::class)->group(function () {
    // Получить свои недавние поиски
    Route::get('/', 'index');
    // Поиск по имени/ссылке пользователя
    Route::get('users', 'users');
    // Поиск по содержимому твита
    Route::get('tweets', 'tweets');
    // Создать недавний поиск
    Route::post('create', 'create');
    // Удалить свои поиски
    Route::delete('clear', 'clear');
});
