<?php

namespace App\Modules\User\Models;

use Database\Factories\UserGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    protected static function newFactory()
    {
        return UserGroupFactory::new();
    }

    // public function resolveRouteBinding($value, $field = null)
    // {
    //     return Cache::remember("group_base_{$value}", 60, function () use ($value, $field) {
    //         return parent::resolveRouteBinding($value, $field);
    //     });
    // }

    public function members()
    {
        return $this->hasMany(UserGroupMember::class, 'user_group_id');
    }

    public function members_data()
    {
        return $this->members()->with('users_data');
    }
}
