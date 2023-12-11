<?php

namespace App\Modules\User\Models;

use App\Prometheus\PrometheusService;
use Database\Factories\UserGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * * Модель, относящаяся к таблице user_group
 * 
 * * Необходима для работы с личными группами пользователей.
 */
class UserGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    protected static function newFactory()
    {
        return UserGroupFactory::new();
    }

    /**
     * @return HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(UserGroupMember::class, 'user_group_id');
    }

    /**
     * @return HasMany
     */
    public function membersData(): HasMany
    {
        return $this->members()->with('usersData');
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('UserGroup');
        });
    }
}
