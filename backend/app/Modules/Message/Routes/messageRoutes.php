<?php

namespace App\Modules\Message\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Message\Controllers\MessageController;

Route::prefix('messages')->middleware(['auth:api'])->controller(MessageController::class)->group(function () {
    Route::prefix('chats')->group(function () {
        Route::get('/', 'chats')->name('getAuthorizedUserChats');
    });

    // Получить сообщения из диалога с пользователем
    Route::get('/{user}', 'index')->name('getMessagesFromChatWithUser');
    // Написать сообщение в диалог с пользователем
    Route::post('/{user}', 'send')->name('sendMessageToChatWithUser');
    // Изменить статус сообщения на прочитано
    Route::patch('/{messageUuid}', 'read')->name('changeMessageStatus');
    // Удалить сообщение (для обеих сторон)
    Route::delete('/{messageUuid}', 'delete')->name('deleteMessage');
});
