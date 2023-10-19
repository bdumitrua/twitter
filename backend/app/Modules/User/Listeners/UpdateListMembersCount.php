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

        // Обновляем счётчик количества пользователей в группе
        $usersList = UsersList::find($usersListMember->users_list_id);
        $usersList->members_count = UsersListMember::where('users_list_id', $usersList->id)->count();
        $usersList->save();
    }
}
