<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserNoticeEvent implements ShouldBroadcast
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
