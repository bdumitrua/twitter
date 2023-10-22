<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UserGroupMember;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserGroupMembersUpdateEvent implements ShouldBroadcast
{
    public $userGroupMember;
    public $add;

    public function __construct(
        UserGroupMember $userGroupMember,
        bool $add,
    ) {
        $this->userGroupMember = $userGroupMember;
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
