<?php

namespace App\Modules\Message\Routes;

use Illuminate\Support\Facades\Route;
use App\Modules\Message\Controllers\MessageController;

Route::prefix('messages')->middleware(['auth:api'])->controller(MessageController::class)->group(function () {
    // Получить сообщения из диалога с пользователем
    Route::get('{user}', 'index')->name('getMessagesFromChatWithUser');
    // Написать сообщение в диалог с пользователем
    Route::post('{user}', 'send')->name('sendMessageToChatWithUser');
    // Изменить статус сообщения на прочитано
    Route::patch('{messageUuid}', 'read')->name('readMessage');
    // Удалить сообщение
    Route::delete('{messageUuid}', 'delete')->name('deleteMessage');

    Route::prefix('chats')->group(function () {
        // Получить свои чаты
        Route::get('/', 'chats')->name('getAuthorizedUserChats');
        // Очистить диалог
        Route::delete('{chat}', 'clear')->name('clearChatMessagesForYou');
    });
});
