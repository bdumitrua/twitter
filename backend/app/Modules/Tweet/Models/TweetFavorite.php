<?php

namespace App\Modules\Tweet\Models;

use App\Prometheus\PrometheusServiceProxy;
use Database\Factories\TweetFavoriteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * * Модель, относящаяся к таблице tweet_favorites
 * 
 * * Необходима для работы с избранными твитами пользователей.
 */
class TweetFavorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tweet_id',
    ];

    protected static function newFactory()
    {
        return TweetFavoriteFactory::new();
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            app(PrometheusServiceProxy::class)->incrementEntityCreatedCount('TweetFavorite');
        });
    }
}
