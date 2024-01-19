<?php

namespace App\Modules\Tweet\Models;

use App\Modules\Tweet\Events\TweetLikeEvent;
use App\Prometheus\PrometheusServiceProxy;
use Database\Factories\TweetLikeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * * Модель, относящаяся к таблице tweet_likes
 * 
 * * Необходима для работы с лайкнутыми твитами пользователей.
 */
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
            app(PrometheusServiceProxy::class)->incrementEntityCreatedCount('TweetLike');

            event(new TweetLikeEvent($tweetLike));
        });
    }
}
