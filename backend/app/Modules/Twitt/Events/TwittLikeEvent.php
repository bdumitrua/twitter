<?php

namespace App\Modules\User\Events;

use App\Modules\Twitt\Models\TwittLike;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TwittLikeEvent implements ShouldBroadcast
{
    public $twittLike;
    public $add;

    public function __construct(
        TwittLike $twittLike,
        bool $add,
    ) {
        $this->twittLike = $twittLike;
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
