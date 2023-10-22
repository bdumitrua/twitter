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
        $add = $event->add;
        $usersList = UsersList::find($usersListSubscribtion->users_list_id);

        if (!empty($add)) {
            $usersList->subsribers_count = $usersList->subsribers_count + 1;
        } else {
            $usersList->subsribers_count = $usersList->subsribers_count - 1;
        }

        // Обновляем счётчик количества пользователей в группе
        $usersList->save();
    }
}
