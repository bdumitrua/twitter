<?php

namespace App\Modules\Notification\Events;

use App\Modules\Notification\Models\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotificationEvent implements ShouldBroadcast
{
    public $notification;

    public function __construct(
        Notification $notification
    ) {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
