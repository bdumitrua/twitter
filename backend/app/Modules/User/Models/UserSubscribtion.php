<?php

namespace App\Modules\User\Models;

use App\Modules\User\Events\UserSubscribtionEvent;
use App\Prometheus\PrometheusService;
use Database\Factories\UserSubscribtionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function subscribers_data()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subscribtions_data()
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($userSubscribtion) {
            app(PrometheusService::class)->incrementEntityCreatedCount('UserSubscribtion');

            event(new UserSubscribtionEvent($userSubscribtion));
        });
    }
}
