<?php

namespace App\Modules\User\Models;

use Database\Factories\UserGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'members_count'
    ];

    protected static function newFactory()
    {
        return UserGroupFactory::new();
    }

    public function members()
    {
        return $this->hasMany(UserGroupMember::class, 'user_group_id')->with('users_data');
    }
}
