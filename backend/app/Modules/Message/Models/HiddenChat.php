<?php

namespace App\Modules\Message\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * * Модель, относящаяся к таблице hidden_chats
 * 
 * * Необходима для хранения чатов, которые нужно скрыть от пользователя. 
 * Например, если в чате не осталось сообщений, то нет смысла показывать его в списке доступных.
 */
class HiddenChat extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id'
    ];
}
