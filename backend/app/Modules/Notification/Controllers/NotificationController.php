<?php

namespace App\Modules\Notification\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    public function read(string $notificationUuid)
    {
        return $this->handleServiceCall(function () use ($notificationUuid) {
            return $this->notificationService->read($notificationUuid);
        });
    }

    public function delete(string $notificationUuid)
    {
        return $this->handleServiceCall(function () use ($notificationUuid) {
            return $this->notificationService->delete($notificationUuid);
        });
    }
}
