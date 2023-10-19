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
        /** @var UserGroupMember */
        $userGroupMember = $event->userGroupMember;

        // Обновляем счётчик количества пользователей в группе
        $group = UserGroup::find($userGroupMember->user_group_id);
        $group->members_count = UserGroupMember::where('user_group_id', $group->id)->count();
        $group->save();
    }
}
