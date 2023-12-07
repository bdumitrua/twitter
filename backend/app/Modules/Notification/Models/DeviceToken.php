<?php

namespace App\Modules\Notification\Models;

use App\Modules\User\Models\User;
use App\Prometheus\PrometheusService;
use Database\Factories\DeviceTokenFactory;
use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusService::class)->incrementEntityCreatedCount('DeviceToken');
        });
    }
}
