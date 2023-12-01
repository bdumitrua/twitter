<?php

namespace App\Modules\User\Events;

use App\Modules\Tweet\Models\TweetLike;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TweetLikeEvent implements ShouldBroadcast
{
    public $tweetLike;

    public function __construct(
        TweetLike $tweetLike
    ) {
        $this->tweetLike = $tweetLike;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
