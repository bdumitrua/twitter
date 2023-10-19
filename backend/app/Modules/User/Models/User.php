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
        NAME,
        'link',
    ];

    protected $fillable = [
        NAME,
        'email',
        'password',
        'link'
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
        return $this->hasMany(UserSubscribtion::class, SUBSCRIBER_ID)->with('subscribtions_data');
    }

    public function subscribers()
    {
        return $this->hasMany(UserSubscribtion::class, USER_ID)->with('subscribers_data');
    }

    public function groups()
    {
        return $this->hasMany(UserGroup::class, USER_ID);
    }

    public function lists()
    {
        return $this->hasMany(UsersList::class, USER_ID);
    }

    public function lists_memberships()
    {
        return $this->hasMany(UsersListMember::class, USER_ID)->with('lists_data');
    }

    public function lists_subscribtions()
    {
        return $this->hasMany(UsersListSubscribtion::class, USER_ID)->with('lists_data');
    }
}
