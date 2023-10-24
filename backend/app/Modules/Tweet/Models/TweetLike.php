<?php

namespace App\Modules\Tweet\Models;

use App\Modules\User\Models\User;
use Database\Factories\TweetFactory;
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
}