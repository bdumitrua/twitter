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
        'type',
        'linked_tweet_id'
    ];

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

    public function favorites()
    {
        return $this->hasMany(TweetFavorite::class, 'tweet_id', 'id');
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
}
