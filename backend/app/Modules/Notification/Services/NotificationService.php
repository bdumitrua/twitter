<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\DTO\NotificationDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Repositories\NotificationRepository;
use App\Modules\Notification\Requests\UpdateNotificationStatusRequest;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    private $notificationRepository;

    public function __construct(
        NotificationRepository $notificationRepository
    ) {
        $this->notificationRepository = $notificationRepository;
    }

    public function index()
    {
        // 
    }

    public function create(NotificationDTO $notificationDTO): void
    {
        // 
    }

    public function update(Notification $notification, UpdateNotificationStatusRequest $updateNotificationStatusRequest): void
    {
        // 
    }
}
