<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\NotificationsSubscribtion;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Collection;

class NotificationsSubscribtionRepository
{
    use GetCachedData;

    protected NotificationsSubscribtion $notificationsSubscribtion;

    public function __construct(NotificationsSubscribtion $notificationsSubscribtion)
    {
        $this->notificationsSubscribtion = $notificationsSubscribtion;
    }
}
