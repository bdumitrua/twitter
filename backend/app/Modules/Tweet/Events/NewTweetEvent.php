<?php

namespace App\Modules\User\Events;

use App\Modules\Tweet\Models\Tweet;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewTweetEvent implements ShouldBroadcast
{
    public $tweet;

    public function __construct(
        Tweet $tweet,
    ) {
        $this->tweet = $tweet;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
