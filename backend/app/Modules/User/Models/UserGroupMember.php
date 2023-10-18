<?php

namespace App\Modules\User\Models;

use Database\Factories\UserGroupMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_group_id',
        'user_id',
    ];

    protected static function newFactory()
    {
        return UserGroupMemberFactory::new();
    }
}
