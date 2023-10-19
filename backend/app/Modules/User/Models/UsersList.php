<?php

namespace App\Modules\User\Models;

use Database\Factories\UsersListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersList extends Model
{
    use HasFactory;

    protected $fillable = [
        USER_ID,
        NAME,
        DESCRIPTION,
        'bg_image',
        'is_private',
    ];

    protected static function newFactory()
    {
        return UsersListFactory::new();
    }
}
