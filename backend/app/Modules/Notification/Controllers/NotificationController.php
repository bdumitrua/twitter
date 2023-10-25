<?php

namespace App\Modules\Notification\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Requests\UpdateNotificationStatusRequest;
use App\Modules\Notification\Services\NotificationService;

class NotificationController extends Controller
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        return $this->handleServiceCall(function () {
            return $this->notificationService->index();
        });
    }

    public function update(Notification $notification, UpdateNotificationStatusRequest $updateNotificationStatusRequest)
    {
        return $this->handleServiceCall(function () use ($notification, $updateNotificationStatusRequest) {
            return $this->notificationService->update($notification, $updateNotificationStatusRequest);
        });
    }
}
