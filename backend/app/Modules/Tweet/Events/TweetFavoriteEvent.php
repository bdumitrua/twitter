<?php

namespace App\Modules\User\Events;

use App\Modules\Tweet\Models\TweetFavorite;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TweetFavoriteEvent implements ShouldBroadcast
{
    public $tweetFavorite;
    public $add;

    public function __construct(
        TweetFavorite $tweetFavorite,
        bool $add
    ) {
        $this->tweetFavorite = $tweetFavorite;
        $this->add = $add;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
