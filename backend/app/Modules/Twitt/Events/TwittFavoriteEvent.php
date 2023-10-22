<?php

namespace App\Modules\User\Events;

use App\Modules\Twitt\Models\TwittFavorite;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TwittFavoriteEvent implements ShouldBroadcast
{
    public $twittFavorite;
    public $add;

    public function __construct(
        TwittFavorite $twittFavorite,
        bool $add
    ) {
        $this->twittFavorite = $twittFavorite;
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
