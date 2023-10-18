<?php

namespace App\Modules\User\Models;

use Database\Factories\UserGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        USER_ID,
        NAME,
        DESCRIPTION,
    ];

    protected static function newFactory()
    {
        return UserGroupFactory::new();
    }
}
