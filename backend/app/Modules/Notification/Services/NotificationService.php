<?php

namespace App\Modules\Notification\Services;

use App\Firebase\FirebaseService;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Repositories\NotificationRepository;
use App\Modules\Notification\Resources\NotificationResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    private NotificationRepository $notificationRepository;
    protected LogManager $logger;
    protected FirebaseService $firebaseService;
    protected ?int $authorizedUserId;

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
        $this->logger->info('Creating notification in firebase', $notificationDTO->toArray());
        $this->notificationRepository->send($notificationDTO);
    }

    public function read(string $notificationUuid): Response
    {
        return $this->notificationRepository->read($notificationUuid, $this->authorizedUserId);
    }

    public function delete(string $notificationUuid): Response
    {
        return $this->notificationRepository->delete($notificationUuid, $this->authorizedUserId);
    }
}
