<?php

namespace App\Modules\User\Events;

use App\Modules\Tweet\Models\TweetLike;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TweetLikeEvent
{
    public $tweetLike;

    public function __construct(
        TweetLike $tweetLike
    ) {
        $this->tweetLike = $tweetLike;
    }
}
