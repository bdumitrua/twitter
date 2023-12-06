<?php

namespace App\Modules\Search\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewSearchEvent implements ShouldBroadcast
{
    public $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
