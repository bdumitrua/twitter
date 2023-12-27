<?php

namespace App\Modules\User\Models;

use App\Modules\Auth\Events\UserCreatedEvent;
use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Notification\Models\NotificationsSubscribtion;
use App\Prometheus\PrometheusService;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * * Модель, относящаяся к таблице users
 * 
 * * Необходима для работы с основными данными пользователей и выстраивания связей.
 * 
 * * Также, соответственно, отвечает за аутентификацию пользователей и валидацию JWT токенов.
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, Searchable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'link',
        'about',
        'bg_image',
        'avatar',
        'status_text',
        'site_url',
        'address',
        'birth_date',
        'token_invalid_before'
    ];

    protected $hidden = [
        'password',
    ];

    protected $searchable = [
        'name',
        'link',
    ];

    protected $casts = [
        'token_invalid_before' => 'datetime:YYYY-MM-DDTHH:MM:SS.uuuuuuZ',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'link' => $this->link,
        ];
    }

    /**
     * @return string
     */
    public function searchableAs(): string
    {
        return 'users';
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
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * @return HasMany
     */
    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function subscribtions(): HasMany
    {
        return $this->hasMany(UserSubscribtion::class, 'subscriber_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function subscribers(): HasMany
    {
        return $this->hasMany(UserSubscribtion::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function subscribersOnNewTweets(): HasMany
    {
        return $this->hasMany(NotificationsSubscribtion::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function subscribtionsData(): HasMany
    {
        return $this->subscribtions()->with('subscribtionsData');
    }

    /**
     * @return HasMany
     */
    public function subscribersData(): HasMany
    {
        return $this->subscribers()->with('subscribersData');
    }

    /**
     * @return HasMany
     */
    public function groupsCreator(): HasMany
    {
        return $this->hasMany(UserGroup::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function groupsMember(): HasMany
    {
        return $this->hasMany(UserGroupMember::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function lists(): HasMany
    {
        return $this->hasMany(UsersList::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function listsSubscribtions(): HasMany
    {
        return $this->hasMany(UsersListSubscribtion::class, 'user_id')->with('listsData');
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($user) {
            app(PrometheusService::class)->incrementEntityCreatedCount('User');

            event(new UserCreatedEvent($user));
        });
    }
}
