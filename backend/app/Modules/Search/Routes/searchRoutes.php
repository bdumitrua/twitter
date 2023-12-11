<?php

namespace App\Modules\Search\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Search\Controllers\SearchController;

Route::prefix('search')->middleware(['auth:api'])->controller(SearchController::class)->group(function () {
    // Получить свои недавние поиски
    Route::get('/', 'index')->name('getAuthorizedUserRecentSearches');
    // Поиск по имени/ссылке пользователя
    Route::get('users', 'users')->name('globalSearchUsers');
    // Поиск по содержимому твита
    Route::get('tweets', 'tweets')->name('globalSearchTweets');
    // Создать недавний поиск
    Route::post('/', 'create')->name('createUserRecentSearch');
    // Удалить свои поиски
    Route::delete('/', 'clear')->name('clearAuthorizedUserRecentSearches');
});
