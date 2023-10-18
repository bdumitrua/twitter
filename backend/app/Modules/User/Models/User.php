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
        return $this->hasMany(UserSubscribtion::class, 'subscriber_id');
    }

    public function subscribers()
    {
        return $this->hasMany(UserSubscribtion::class, 'user_id');
    }
}
