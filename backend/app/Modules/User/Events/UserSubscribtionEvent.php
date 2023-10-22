<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserSubscribtionEvent implements ShouldBroadcast
{
    public $userSubscribtion;
    public $add;

    public function __construct(
        UserSubscribtion $userSubscribtion,
        bool $add,
    ) {
        $this->userSubscribtion = $userSubscribtion;
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
