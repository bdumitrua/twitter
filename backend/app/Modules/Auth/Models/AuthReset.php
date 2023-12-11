<?php

namespace App\Modules\Auth\Models;

use App\Modules\User\Models\User;
use App\Prometheus\PrometheusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AuthReset extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'confirmed'
    ];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('AuthReset');
        });
    }
}
