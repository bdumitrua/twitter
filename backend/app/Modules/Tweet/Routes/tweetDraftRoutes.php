<?php

namespace App\Modules\Tweet\Routes;

use App\Modules\Tweet\Controllers\TweetDraftController;
use Illuminate\Support\Facades\Route;

Route::prefix('tweets/drafts')->middleware(['auth:api'])->controller(TweetDraftController::class)->group(function () {
    Route::get('/', 'index')->name('get_authorized_user_drafts');
    Route::post('/', 'create')->name('create_tweet_draft');
    Route::delete('/', 'delete')->name('delete_tweet_draft');
});
