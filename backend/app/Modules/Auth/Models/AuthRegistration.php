<?php

namespace App\Modules\Auth\Models;

use App\Modules\Auth\Events\RegistrationStartedEvent;
use App\Prometheus\PrometheusService;
use Illuminate\Database\Eloquent\Model;

class AuthRegistration extends Model
{
    protected $fillable = [
        'code',
        'name',
        'email',
        'birth_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registrationData) {
            app(PrometheusService::class)->incrementEntityCreatedCount('AuthRegistration');

            event(new RegistrationStartedEvent($registrationData));
        });
    }
}
