<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UsersListSubscribtion;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UsersListSubscribtionEvent
{
    public $usersListId;

    public function __construct(
        int $usersListId
    ) {
        $this->usersListId = $usersListId;
    }
}
