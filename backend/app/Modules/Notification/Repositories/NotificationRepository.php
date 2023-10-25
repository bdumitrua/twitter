<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Notification\Models\Notification;

class NotificationRepository
{
    protected $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }
}
