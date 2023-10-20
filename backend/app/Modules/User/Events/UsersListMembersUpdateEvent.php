<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UsersListMember;
use App\Modules\User\Models\UsersListSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UsersListMembersUpdateEvent implements ShouldBroadcast
{
    public $usersListMember;

    public function __construct(
        UsersListMember $usersListMember,
    ) {
        $this->usersListMember = $usersListMember;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}