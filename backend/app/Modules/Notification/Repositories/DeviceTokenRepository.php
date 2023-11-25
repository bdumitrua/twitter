<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Notification\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class DeviceTokenRepository
{
    protected $deviceToken;

    public function __construct(DeviceToken $deviceToken)
    {
        $this->deviceToken = $deviceToken;
    }
}
