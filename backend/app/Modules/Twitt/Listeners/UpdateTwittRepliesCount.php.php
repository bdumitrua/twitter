<?php

namespace App\Modules\User\Listeners;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UserSubscribtion;

class UpdateGroupMembersCount
{
    public function handle($event)
    {
        /** @var Twitt */
        $twitt = $event->twitt;
        $twitt->replies_count = $twitt->replies_count + 1;
        $twitt->save();
    }
}
