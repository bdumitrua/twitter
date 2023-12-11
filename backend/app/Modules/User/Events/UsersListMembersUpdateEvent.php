<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UsersListMember;
use App\Modules\User\Models\UsersListSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UsersListMembersUpdateEvent
{
    public $usersListMember;

    public function __construct(
        UsersListMember $usersListMember,
    ) {
        $this->usersListMember = $usersListMember;
    }
}
