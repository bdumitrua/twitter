<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UsersListSubscribtion;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UsersListSubscribtionEvent implements ShouldBroadcast
{
    public $usersListSubscribtion;
    public $add;

    public function __construct(
        UsersListSubscribtion $usersListSubscribtion,
        bool $add
    ) {
        $this->usersListSubscribtion = $usersListSubscribtion;
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
