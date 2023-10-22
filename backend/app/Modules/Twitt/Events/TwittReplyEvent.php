<?php

namespace App\Modules\User\Events;

use App\Modules\Twitt\Models\Twitt;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TwittReplyEvent implements ShouldBroadcast
{
    public $twitt;

    public function __construct(
        Twitt $twitt,
    ) {
        $this->twitt = $twitt;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
