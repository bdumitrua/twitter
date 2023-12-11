<?php

namespace App\Modules\Notification\Models;

use App\Modules\User\Models\User;
use App\Prometheus\PrometheusService;
use Database\Factories\DeviceTokenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * * Модель, относящаяся к таблице device_tokens
 * 
 * * Необходима для работы с девайс токенами пользователей.
 * 
 * * Девайс токены же нужны для отправки уведомлений на устройства пользователей.
 */
class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
    ];

    protected static function newFactory()
    {
        return DeviceTokenFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('DeviceToken');
        });
    }
}
