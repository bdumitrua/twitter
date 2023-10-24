<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UserGroupMember;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserGroupMembersUpdateEvent implements ShouldBroadcast
{
    public $userGroupId;
    public $add;

    public function __construct(
        int $userGroupId,
        bool $add,
    ) {
        $this->userGroupId = $userGroupId;
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
