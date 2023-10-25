<?php

namespace App\Modules\User\Models;

use Database\Factories\UsersListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class UsersList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'bg_image',
        'is_private',
    ];

    protected static function newFactory()
    {
        return UsersListFactory::new();
    }

    // public function resolveRouteBinding($value, $field = null)
    // {
    //     return Cache::remember("users_list_base_{$value}", 60, function () use ($value, $field) {
    //         return parent::resolveRouteBinding($value, $field);
    //     });
    // }

    public function members()
    {
        return $this->hasMany(UsersListMember::class, 'users_list_id');
    }

    public function subscribers()
    {
        return $this->hasMany(UsersListSubscribtion::class, 'users_list_id');
    }

    public function members_data()
    {
        return $this->members()->with('users_data');
    }

    public function subscribers_data()
    {
        return $this->subscribers()->with('users_data');
    }
}
