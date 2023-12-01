<?php

namespace App\Modules\User\Events;

use App\Modules\Tweet\Models\Tweet;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TweetReplyEvent implements ShouldBroadcast
{
    public $tweetId;

    public function __construct(
        int $tweetId
    ) {
        $this->tweetId = $tweetId;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
