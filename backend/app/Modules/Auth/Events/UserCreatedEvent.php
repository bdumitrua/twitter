<?php

namespace App\Modules\Auth\Events;

use App\Modules\User\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserCreatedEvent implements ShouldBroadcast
{
    public $user;

    public function __construct(
        User $user,
    ) {
        $this->user = $user;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
