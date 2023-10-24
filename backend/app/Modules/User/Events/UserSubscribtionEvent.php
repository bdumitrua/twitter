<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserSubscribtionEvent implements ShouldBroadcast
{
    public $userSubscribtion;

    public function __construct(
        UserSubscribtion $userSubscribtion,
    ) {
        $this->userSubscribtion = $userSubscribtion;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
