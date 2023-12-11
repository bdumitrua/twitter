<?php

namespace App\Modules\User\Models;

use App\Prometheus\PrometheusService;
use Database\Factories\UsersListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * * Модель, относящаяся к таблице users_lists
 * 
 * * Необходима для работы со списками пользователей.
 */
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

    /**
     * @return HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(UsersListMember::class, 'users_list_id');
    }

    /**
     * @return HasMany
     */
    public function subscribers(): HasMany
    {
        return $this->hasMany(UsersListSubscribtion::class, 'users_list_id');
    }

    /**
     * @return HasMany
     */
    public function members_data(): HasMany
    {
        return $this->members()->with('users_data');
    }

    /**
     * @return HasMany
     */
    public function subscribers_data(): HasMany
    {
        return $this->subscribers()->with('users_data');
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('UsersList');
        });
    }
}
