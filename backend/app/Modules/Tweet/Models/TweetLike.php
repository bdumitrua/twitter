<?php

namespace App\Modules\Tweet\Models;

use App\Modules\User\Events\TweetLikeEvent;
use App\Prometheus\PrometheusService;
use Database\Factories\TweetLikeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TweetLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tweet_id',
    ];

    protected static function newFactory()
    {
        return TweetLikeFactory::new();
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($tweetLike) {
            app(PrometheusService::class)->incrementEntityCreatedCount('TweetLike');

            event(new TweetLikeEvent($tweetLike));
        });
    }
}
