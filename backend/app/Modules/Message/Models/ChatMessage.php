<?php

namespace App\Modules\Message\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * * Модель, относящаяся к таблице сhat_messages
 * 
 * * Необходима для хранения связи чат-сообщение, для поиска чата по сообщению и наоборот.
 */
class ChatMessage extends Model
{
    protected $fillable = [
        'chat_id',
        'message_uuid'
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }
}
