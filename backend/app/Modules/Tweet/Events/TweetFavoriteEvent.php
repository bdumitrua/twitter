<?php

namespace App\Modules\Tweet\Events;

use App\Modules\Tweet\Models\TweetFavorite;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TweetFavoriteEvent
{
    public $tweetFavorite;

    public function __construct(
        TweetFavorite $tweetFavorite
    ) {
        $this->tweetFavorite = $tweetFavorite;
    }
}
