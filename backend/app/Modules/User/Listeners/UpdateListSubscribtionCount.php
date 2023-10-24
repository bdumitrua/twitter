<?php

namespace App\Modules\User\Listeners;

use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListSubscribtion;

class UpdateListSubscribtionCount
{
    public function handle($event)
    {
        $usersList = UsersList::find($event->usersListId);

        if (!empty($event->add)) {
            $usersList->subsribers_count = $usersList->subsribers_count + 1;
        } else {
            $usersList->subsribers_count = $usersList->subsribers_count - 1;
        }

        $usersList->save();
    }
}
