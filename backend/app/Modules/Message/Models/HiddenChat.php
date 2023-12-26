<?php

namespace App\Modules\Message\Models;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HiddenChat extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id'
    ];
}
