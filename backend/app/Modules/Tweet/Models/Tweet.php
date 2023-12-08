<?php

namespace App\Modules\Tweet\Models;

use App\Modules\User\Events\NewTweetEvent;
use App\Modules\User\Models\User;
use App\Prometheus\PrometheusService;
use Database\Factories\TweetFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Tweet extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'user_id',
        'user_group_id',
        'text',
        'type',
        'linked_tweet_id'
    ];

    protected $searchable = [
        'text',
    ];

    public function toSearchableArray()
    {
        return [
            'text' => $this->text,
        ];
    }

    public function searchableAs()
    {
        return 'tweets';
    }

    protected static function newFactory()
    {
        return TweetFactory::new();
    }

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(TweetLike::class, 'tweet_id', 'id');
    }

    public function notices()
    {
        return $this->hasMany(TweetNotice::class, 'tweet_id', 'id');
    }

    public function favorites()
    {
        return $this->hasMany(TweetFavorite::class, 'tweet_id', 'id');
    }

    public function linkedTweet()
    {
        return $this->hasOne(Tweet::class, 'id', 'linked_tweet_id');
    }

    public function replies()
    {
        return $this->hasMany(Tweet::class, 'linked_tweet_id', 'id')
            ->where('type', 'reply');
    }

    public function reposts()
    {
        return $this->hasMany(Tweet::class, 'linked_tweet_id', 'id')
            ->where('type', 'repost');
    }

    public function quotes()
    {
        return $this->hasMany(Tweet::class, 'linked_tweet_id', 'id')
            ->where('type', 'quote');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($tweet) {
            app(PrometheusService::class)->incrementEntityCreatedCount('Tweet');

            event(new NewTweetEvent($tweet));
        });
    }
}
