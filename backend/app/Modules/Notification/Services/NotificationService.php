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
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    private $notificationRepository;
    private $authorizedUserId;

    public function __construct(
        NotificationRepository $notificationRepository
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->authorizedUserId = Auth::id();
    }

    public function index(): Collection
    {
        return $this->notificationRepository->getByUserId($this->authorizedUserId);
    }

    public function create(NotificationDTO $notificationDTO): void
    {
        $this->notificationRepository->create($notificationDTO);
    }

    public function update(Notification $notification, UpdateNotificationStatusRequest $updateNotificationStatusRequest): void
    {
        $this->notificationRepository->update($notification, $updateNotificationStatusRequest->status);
    }
}
