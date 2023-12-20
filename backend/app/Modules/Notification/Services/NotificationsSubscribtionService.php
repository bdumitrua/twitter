<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Modules\Notification\Repositories\DeviceTokenRepository;
use App\Modules\Notification\Repositories\NotificationsSubscribtionRepository;
use App\Modules\Notification\Requests\DeviceTokenRequest;
use App\Modules\Notification\Resources\DeviceTokenResource;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

class NotificationsSubscribtionService
{
    protected NotificationsSubscribtionRepository $notificationsSubscribtionRepository;
    protected LogManager $logger;
    protected ?int $authorizedUserId;

    public function __construct(
        NotificationsSubscribtionRepository $notificationsSubscribtionRepository,
        LogManager $logger,
    ) {
        $this->notificationsSubscribtionRepository = $notificationsSubscribtionRepository;
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    /**
     * @param User $user
     * 
     * @return Response
     */
    public function subscribe(User $user): Response
    {
        return $this->notificationsSubscribtionRepository->subscribe($user->id, $this->authorizedUserId);
    }

    /**
     * @param User $user
     * 
     * @return Response
     */
    public function unsubscribe(User $user): Response
    {
        return $this->notificationsSubscribtionRepository->unsubscribe($user->id, $this->authorizedUserId);
    }
}
