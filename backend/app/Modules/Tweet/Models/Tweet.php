<?php

namespace App\Modules\Tweet\Models;

use App\Modules\User\Models\User;
use Database\Factories\TweetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_group_id',
        'text',
        'is_comment',
        'commented_tweet_id',
        'is_reply',
        'replied_tweet_id',
        'is_repost',
        'reposted_tweet_id',
    ];

    protected static function newFactory()
    {
        return TweetFactory::new();
    }

    // public function resolveRouteBinding($value, $field = null)
    // {
    //     return Cache::remember("tweet_base_{$value}", 60, function () use ($value, $field) {
    //         return parent::resolveRouteBinding($value, $field);
    //     });
    // }

    public function author()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(TweetLike::class, 'tweet_id', 'id');
    }

    public function favorites()
    {
        return $this->hasMany(TweetFavorite::class, 'tweet_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(Tweet::class, 'replied_tweet_id', 'id');
    }

    public function reposts()
    {
        return $this->hasMany(Tweet::class, 'reposted_tweet_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Tweet::class, 'commented_tweet_id', 'id');
    }
}
