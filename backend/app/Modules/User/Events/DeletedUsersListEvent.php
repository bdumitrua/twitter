<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UsersList;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeletedUsersListEvent implements ShouldBroadcast
{
    public $usersList;

    public function __construct(
        UsersList $usersList,
    ) {
        $this->usersList = $usersList;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
