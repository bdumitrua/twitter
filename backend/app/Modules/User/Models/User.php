<?php

namespace App\Modules\User\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Elastic\ScoutDriverPlus\Searchable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, Searchable;

    protected $searchable = [
        'name',
        'link',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'link',
        'bg_image',
        'avatar',
        'status_text',
        'site_url',
        'address',
        'subscribtions_count',
        'subscribers_count',
        'birth_date',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function searchableAs()
    {
        return 'users';
    }

    public function subscribtions()
    {
        return $this->hasMany(UserSubscribtion::class, 'subscriber_id')->with('subscribtions_data');
    }

    public function subscribers()
    {
        return $this->hasMany(UserSubscribtion::class, 'user_id')->with('subscribers_data');
    }

    public function groups()
    {
        return $this->hasMany(UserGroup::class, 'user_id');
    }

    public function lists()
    {
        return $this->hasMany(UsersList::class, 'user_id');
    }

    public function lists_memberships()
    {
        return $this->hasMany(UsersListMember::class, 'user_id')->with('lists_data');
    }

    public function lists_subscribtions()
    {
        return $this->hasMany(UsersListSubscribtion::class, 'user_id')->with('lists_data');
    }
}
