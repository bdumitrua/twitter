<?php

namespace App\Modules\User\Models;

use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Notification\Models\Notification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, Searchable, SoftDeletes;

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
        'birth_date',
    ];

    protected $hidden = [
        'password',
    ];

    protected $searchable = [
        'name',
        'link',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'link' => $this->link,
        ];
    }

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

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class, 'user_id', 'id');
    }

    public function subscribtions()
    {
        return $this->hasMany(UserSubscribtion::class, 'subscriber_id', 'id');
    }

    public function subscribers()
    {
        return $this->hasMany(UserSubscribtion::class, 'user_id', 'id');
    }

    public function subscribtions_data()
    {
        return $this->subscribtions()->with('subscribtions_data');
    }

    public function subscribers_data()
    {
        return $this->subscribers()->with('subscribers_data');
    }

    public function groups_creator()
    {
        return $this->hasMany(UserGroup::class, 'user_id');
    }

    public function groups_member()
    {
        return $this->hasMany(UserGroupMember::class, 'user_id');
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

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id');
    }
}
