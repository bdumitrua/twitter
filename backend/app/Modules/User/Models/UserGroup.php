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
        'members_count'
    ];

    protected static function newFactory()
    {
        return UserGroupFactory::new();
    }

    public function members()
    {
        return $this->hasMany(UserGroupMember::class, USER_GROUP_ID)->with('users_data');
    }
}
