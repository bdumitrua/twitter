<?php

namespace App\Modules\Auth\Models;

use App\Modules\Auth\Events\RegistrationStartedEvent;
use App\Prometheus\PrometheusService;
use Illuminate\Database\Eloquent\Model;

/**
 * * Модель, относящаяся к таблице auth_registrations
 * 
 * * Необходима для хранения промежуточных данных в процессе регистрации новых пользователей.
 */
class AuthRegistration extends Model
{
    protected $fillable = [
        'code',
        'name',
        'email',
        'birth_date',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($registrationData) {
            app(PrometheusService::class)->incrementEntityCreatedCount('AuthRegistration');

            event(new RegistrationStartedEvent($registrationData));
        });
    }
}
