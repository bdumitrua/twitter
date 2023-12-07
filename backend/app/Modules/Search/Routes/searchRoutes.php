<?php

namespace App\Modules\Search\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Search\Controllers\SearchController;

Route::prefix('search')->middleware(['auth:api'])->controller(SearchController::class)->group(function () {
    // Получить свои недавние поиски
    Route::get('/', 'index')->name('get_authorized_user_recent_searches');
    // Поиск по имени/ссылке пользователя
    Route::get('users', 'users')->name('global_search_users');
    // Поиск по содержимому твита
    Route::get('tweets', 'tweets')->name('global_search_tweets');
    // Создать недавний поиск
    Route::post('create', 'create')->name('create_user_recent_search');
    // Удалить свои поиски
    Route::delete('clear', 'clear')->name('clear_authorized_user_recent_searches');
});
