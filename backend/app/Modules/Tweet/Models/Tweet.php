<?php

namespace App\Modules\Tweet\Models;

use App\Modules\Tweet\Events\NewTweetEvent;
use App\Modules\User\Models\User;
use App\Prometheus\PrometheusServiceProxy;
use Database\Factories\TweetFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * * Модель, относящаяся к таблице tweets
 * 
 * * Необходима для работы с основными данными твитов и образования связей.
 */
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

    protected static function newFactory()
    {
        return TweetFactory::new();
    }

    protected $searchable = [
        'text',
    ];

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'text' => $this->text,
        ];
    }

    /**
     * @return string
     */
    public function searchableAs(): string
    {
        return 'tweets';
    }

    /**
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(TweetLike::class, 'tweet_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function notices(): HasMany
    {
        return $this->hasMany(TweetNotice::class, 'tweet_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(TweetFavorite::class, 'tweet_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function linkedTweet(): BelongsTo
    {
        return $this->belongsTo(Tweet::class, 'linked_tweet_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function threadChild(): HasOne
    {
        return $this->hasOne(Tweet::class, 'linked_tweet_id', 'id')
            ->where('type', 'thread');
    }

    /**
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Tweet::class, 'linked_tweet_id', 'id')
            ->where('type', 'reply');
    }

    /**
     * @return HasMany
     */
    public function reposts(): HasMany
    {
        return $this->hasMany(Tweet::class, 'linked_tweet_id', 'id')
            ->where('type', 'repost');
    }

    /**
     * @return HasMany
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Tweet::class, 'linked_tweet_id', 'id')
            ->where('type', 'quote');
    }

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($tweet) {
            app(PrometheusServiceProxy::class)->incrementEntityCreatedCount('Tweet');

            event(new NewTweetEvent($tweet));
        });
    }
}
