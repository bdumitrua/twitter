<?php

namespace App\Modules\Notification\Models;

use App\Prometheus\PrometheusServiceProxy;
use Illuminate\Database\Eloquent\Model;

/**
 * * Модель, относящаяся к таблице user_notifications
 * 
 * * Необходима для сохранения связи пользователь-уведомление.
 */
class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'notification_uuid',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            app(PrometheusServiceProxy::class)->incrementEntityCreatedCount('UserNotification');
        });
    }
}
