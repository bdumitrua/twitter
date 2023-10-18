<?php

namespace App\Modules\User\Models;

use Database\Factories\UserSubscribtionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscribtion extends Model
{
    use HasFactory;

    protected $fillable = [
        USER_ID,
        SUBSCRIBER_ID,
    ];

    protected static function newFactory()
    {
        return UserSubscribtionFactory::new();
    }
}
