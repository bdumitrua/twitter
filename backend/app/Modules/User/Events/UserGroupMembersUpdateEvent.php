<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UserGroupMember;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserGroupMembersUpdateEvent
{
    public $userGroupId;

    public function __construct(
        int $userGroupId,
    ) {
        $this->userGroupId = $userGroupId;
    }
}
