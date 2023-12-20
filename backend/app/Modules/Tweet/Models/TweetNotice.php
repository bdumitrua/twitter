<?php

namespace App\Modules\Tweet\Models;

use App\Modules\Tweet\Events\TweetNoticeEvent;
use App\Prometheus\PrometheusService;
use Illuminate\Database\Eloquent\Model;

/* 
    Т.к. упоминание можно только создать (изменить логически нельзя, 
    а для удаления необходимо изменить твит (что на данный момент невозможно сделать)),
    то для него создана только таблица и модель, без путей, сервиса и т.д.
*/

/**
 * * Модель, относящаяся к таблице tweet_notices
 * 
 * * Необходима для работы с упоминаниями пользователей в твитах.
 * 
 *  Т.к. упоминание можно только создать (изменить логически нельзя, 
 *  а для удаления необходимо изменить твит (что на данный момент невозможно сделать)),
 *  то для него создана только таблица и модель, без путей, сервиса и т.д.
 */
class TweetNotice extends Model
{
    protected $fillable = [
        'link',
        'user_id',
        'tweet_id',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($tweetNotice) {
            app(PrometheusService::class)->incrementEntityCreatedCount('TweetNotice');

            event(new TweetNoticeEvent($tweetNotice));
        });
    }
}
