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

    public function members()
    {
        return $this->hasMany(UsersListMember::class, 'users_list_id')->with('users_data');
    }

    public function subscribers()
    {
        return $this->hasMany(UsersListSubscribtion::class, 'users_list_id')->with('users_data');
    }
}
