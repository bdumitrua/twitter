<?php

namespace App\Modules\Auth\Models;

use App\Modules\User\Models\User;
use App\Prometheus\PrometheusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * * Модель, относящаяся к таблице auth_resets
 * 
 * * Необходима для хранения промежуточных данных в процессе сброса пароля аккаунта.
 */
class AuthReset extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'confirmed'
    ];

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

        static::creating(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('AuthReset');
        });
    }
}
