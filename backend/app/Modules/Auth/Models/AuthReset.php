<?php

namespace App\Modules\Auth\Models;

use App\Modules\User\Models\User;
use App\Prometheus\PrometheusServiceProxy;
use Database\Factories\AuthResetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * * Модель, относящаяся к таблице auth_resets
 * 
 * * Необходима для хранения промежуточных данных в процессе сброса пароля аккаунта.
 */
class AuthReset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'confirmed'
    ];

    protected static function newFactory()
    {
        return AuthResetFactory::new();
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

        static::creating(function ($model) {
            app(PrometheusServiceProxy::class)->incrementEntityCreatedCount('AuthReset');
        });
    }
}
