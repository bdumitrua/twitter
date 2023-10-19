<?php

namespace App\Modules\User\Models;

use Database\Factories\UsersListSubscribtionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersListSubscribtion extends Model
{
    use HasFactory;

    protected $fillable = [
        USER_ID,
        'users_list_id'
    ];

    protected static function newFactory()
    {
        return UsersListSubscribtionFactory::new();
    }
}
