<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UserGroupMember;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserGroupMembersUpdateEvent implements ShouldBroadcast
{
    public $userGroupMember;

    public function __construct(
        UserGroupMember $userGroupMember,
    ) {
        $this->userGroupMember = $userGroupMember;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
