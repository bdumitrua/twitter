<?php

namespace App\Modules\User\Events;

use App\Modules\Tweet\Models\Tweet;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TweetReplyEvent
{
    public $tweetId;

    public function __construct(
        int $tweetId
    ) {
        $this->tweetId = $tweetId;
    }
}
