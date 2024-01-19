<?php

namespace App\Modules\Notification\Services;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Modules\Notification\Repositories\NotificationsSubscribtionRepository;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserSubscribtionRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

class NotificationsSubscribtionService
{
    protected NotificationsSubscribtionRepository $notificationsSubscribtionRepository;
    protected UserSubscribtionRepository $userSubscribtionRepository;
    protected LogManager $logger;
    protected ?int $authorizedUserId;

    public function __construct(
        NotificationsSubscribtionRepository $notificationsSubscribtionRepository,
        UserSubscribtionRepository $userSubscribtionRepository,
        LogManager $logger,
    ) {
        $this->notificationsSubscribtionRepository = $notificationsSubscribtionRepository;
        $this->userSubscribtionRepository = $userSubscribtionRepository;
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
        if (empty($this->userSubscribtionRepository->getByBothIds($user->id, $this->authorizedUserId))) {
            return ResponseHelper::forbiddenRequest();
        }

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
