<?php

namespace App\Modules\User\Events;

use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserSubscribtionEvent
{
    public $userSubscribtion;

    public function __construct(
        UserSubscribtion $userSubscribtion,
    ) {
        $this->userSubscribtion = $userSubscribtion;
    }
}
