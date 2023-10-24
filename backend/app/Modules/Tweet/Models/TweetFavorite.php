<?php

namespace App\Modules\Tweet\Models;

use App\Modules\User\Models\User;
use Database\Factories\TweetFavoriteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
