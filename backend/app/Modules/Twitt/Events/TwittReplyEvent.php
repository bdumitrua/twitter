<?php

namespace App\Modules\User\Events;

use App\Modules\Twitt\Models\Twitt;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TwittReplyEvent implements ShouldBroadcast
{
    public $twittId;
    public $add;

    public function __construct(
        int $twittId,
        bool $add,
    ) {
        $this->twittId = $twittId;
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