<?php

namespace App\Modules\Twitt\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewTwittEvent implements ShouldBroadcast
{
    public $twitt;

    public function __construct($twitt)
    {
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
