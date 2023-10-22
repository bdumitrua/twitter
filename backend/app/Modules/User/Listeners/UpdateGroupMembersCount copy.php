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
        $add = $event->add;
        $group = UserGroup::find($userGroupMember->user_group_id);

        if (!empty($add)) {
            $group->members_count = $group->members_count + 1;
        } else {
            $group->members_count = $group->members_count - 1;
        }

        // Обновляем счётчик количества пользователей в группе
        $group->save();
    }
}
