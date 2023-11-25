<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\DTO\NotificationDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Repositories\DeviceTokenRepository;
use App\Modules\Notification\Requests\UpdateNotificationStatusRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DeviceTokenService
{
    private $deviceTokenRepository;

    public function __construct(
        DeviceTokenRepository $deviceTokenRepository
    ) {
        $this->deviceTokenRepository = $deviceTokenRepository;
    }
}
