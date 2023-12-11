<?php

namespace App\Modules\Notification\Services;

use App\Firebase\FirebaseService;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Repositories\NotificationRepository;
use App\Modules\Notification\Requests\UpdateNotificationStatusRequest;
use App\Modules\Notification\Resources\NotificationResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    private NotificationRepository $notificationRepository;
    protected LogManager $logger;
    protected FirebaseService $firebaseService;
    private ?int $authorizedUserId;

    public function __construct(
        NotificationRepository $notificationRepository,
        FirebaseService $firebaseService,
        LogManager $logger,
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->firebaseService = $firebaseService;
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    /**
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return NotificationResource::collection(
            $this->notificationRepository->getByUserId($this->authorizedUserId)
        );
    }

    /**
     * @param NotificationDTO $notificationDTO
     * 
     * @return void
     */
    public function create(NotificationDTO $notificationDTO): void
    {
        $this->logger->info('Creating notification from request DTO', $notificationDTO->toArray());
        $newNotification = $this->notificationRepository->create($notificationDTO);

        $this->logger->info('Sending notification to firebase', $newNotification->toArray());
        $this->firebaseService->storeNotification($newNotification);
    }

    /**
     * @param Notification $notification
     * @param UpdateNotificationStatusRequest $updateNotificationStatusRequest
     * 
     * @return void
     */
    public function update(Notification $notification, UpdateNotificationStatusRequest $updateNotificationStatusRequest): void
    {
        $this->logger->info(
            'Updating notification from update request',
            [
                'notification_uuid' => $notification->uuid,
                'Request' => $updateNotificationStatusRequest->toArray()
            ]
        );
        $this->notificationRepository->update($notification, $updateNotificationStatusRequest->status);
    }
}
