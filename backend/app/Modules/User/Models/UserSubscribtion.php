<?php

namespace App\Modules\User\Models;

use App\Modules\User\Events\UserSubscribtionEvent;
use App\Prometheus\PrometheusService;
use Database\Factories\UserSubscribtionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * * Модель, относящаяся к таблице user_subscribtions
 * 
 * * Необходима для хранения списка подписчиков пользователей.
 */
class UserSubscribtion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subscriber_id',
    ];

    protected static function newFactory()
    {
        return UserSubscribtionFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function subscribersData(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function subscribtionsData(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($userSubscribtion) {
            app(PrometheusService::class)->incrementEntityCreatedCount('UserSubscribtion');

            event(new UserSubscribtionEvent($userSubscribtion));
        });
    }
}
