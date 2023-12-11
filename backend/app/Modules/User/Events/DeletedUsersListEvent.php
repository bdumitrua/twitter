<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UsersList;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeletedUsersListEvent
{
    public $usersList;

    public function __construct(
        array $usersList,
    ) {
        $this->usersList = $usersList;
    }
}
