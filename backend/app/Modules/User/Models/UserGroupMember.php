<?php

namespace App\Modules\User\Models;

use Database\Factories\UserGroupMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        USER_GROUP_ID,
        USER_ID,
    ];

    protected static function newFactory()
    {
        return UserGroupMemberFactory::new();
    }

    public function users_data()
    {
        return $this->belongsTo(User::class, USER_ID);
    }
}
