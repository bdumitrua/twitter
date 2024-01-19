<?php

namespace App\Modules\Message\Models;

use Illuminate\Database\Eloquent\Model;

class HiddenChat extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id'
    ];
}
