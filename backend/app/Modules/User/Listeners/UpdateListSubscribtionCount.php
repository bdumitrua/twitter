<?php

namespace App\Modules\User\Listeners;

use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListSubscribtion;

class UpdateListSubscribtionCount
{
    public function handle($event)
    {
        /** @var UsersListSubscribtion */
        $usersListSubscribtion = $event->usersListSubscribtion;

        // Обновляем счётчик количества пользователей в группе
        $usersList = UsersList::find($usersListSubscribtion->users_list_id);
        $usersList->subsribers_count = UsersListSubscribtion::where('users_list_id', $usersList->id)->count();
        $usersList->save();
    }
}
