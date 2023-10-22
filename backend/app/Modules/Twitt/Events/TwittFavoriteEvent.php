<?php

namespace App\Modules\User\Events;

use App\Modules\Twitt\Models\TwittFavorite;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TwittFavoriteEvent implements ShouldBroadcast
{
    public $twittFavorite;

    public function __construct(
        TwittFavorite $twittFavorite,
    ) {
        $this->twittFavorite = $twittFavorite;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
