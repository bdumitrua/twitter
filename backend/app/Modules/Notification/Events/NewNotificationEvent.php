<?php

namespace App\Modules\Notification\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewNotificationEvent
{
    public $notification;

    public function __construct(
        $notification
    ) {
        $this->notification = $notification;
    }
}
