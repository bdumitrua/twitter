<?php

namespace App\Modules\Tweet\Events;

use App\Modules\Tweet\Models\Tweet;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TweetRepostEvent
{
    public $tweetId;

    public function __construct(
        int $tweetId,
    ) {
        $this->tweetId = $tweetId;
    }
}
