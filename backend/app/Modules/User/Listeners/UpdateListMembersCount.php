<?php

namespace App\Modules\User\Listeners;

use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListMember;

class UpdateListMembersCount
{
    public function handle($event)
    {
        /** @var UsersListMember */
        $usersListMember = $event->usersListMember;
        $add = $event->add;
        $usersList = UsersList::find($usersListMember->users_list_id);

        if (!empty($add)) {
            $usersList->members_count = $usersList->members_count + 1;
        } else {
            $usersList->members_count = $usersList->members_count - 1;
        }

        // Обновляем счётчик количества пользователей в группе
        $usersList->save();
    }
}
