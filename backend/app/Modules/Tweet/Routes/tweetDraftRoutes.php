<?php

namespace App\Modules\Tweet\Routes;

use App\Modules\Tweet\Controllers\TweetDraftController;
use Illuminate\Support\Facades\Route;

Route::prefix('tweets/drafts')->middleware(['auth:api'])->controller(TweetDraftController::class)->group(function () {
    Route::get('/', 'index')->name('getAuthorizedUserDrafts');
    Route::post('/', 'create')->name('createTweetDraft');
    Route::delete('/', 'delete')->name('deleteTweetDrafts');
});
