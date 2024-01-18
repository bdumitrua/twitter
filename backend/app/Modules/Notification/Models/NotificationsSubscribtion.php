<?php

namespace App\Modules\Notification\Models;

use App\Prometheus\PrometheusServiceProxy;
use Illuminate\Database\Eloquent\Model;

/**
 * * Модель, относящаяся к таблице notifications_subscribtions
 * 
 * * Необходима для подписки пользователей на новые твиты других пользователей.
 */
class NotificationsSubscribtion extends Model
{
    protected $fillable = [
        'user_id',
        'subscriber_id',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            app(PrometheusServiceProxy::class)->incrementEntityCreatedCount('NotificationsSubscriber');
        });
    }
}
