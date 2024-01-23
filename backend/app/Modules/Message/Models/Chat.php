<?php

namespace App\Modules\Message\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * * Модель, относящаяся к таблице chats
 * 
 * * Необходима для хранения списка участников в чате.
 */
class Chat extends Model
{
    protected $fillable = [
        'first_user_id',
        'second_user_id'
    ];

    public function firstUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_user_id');
    }

    public function secondUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'second_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_id', 'id');
    }

    // Метод для получения обоих пользователей
    public function participants(): Collection
    {
        return collect([$this->firstUser, $this->secondUser]);
    }
}
