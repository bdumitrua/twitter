<?php

namespace App\Modules\User\Services;

use App\Modules\Notification\Services\NotificationsSubscribtionService;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Repositories\UserSubscribtionRepository;
use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserSubscribtionService
{
    protected NotificationsSubscribtionService $notificationsSubscribtionService;
    protected UserSubscribtionRepository $userSubscribtionRepository;
    protected UserRepository $userRepository;
    protected ?int $authorizedUserId;

    public function __construct(
        NotificationsSubscribtionService $notificationsSubscribtionService,
        UserSubscribtionRepository $userSubscribtionRepository,
        UserRepository $userRepository,
    ) {
        $this->notificationsSubscribtionService = $notificationsSubscribtionService;
        $this->userSubscribtionRepository = $userSubscribtionRepository;
        $this->userRepository = $userRepository;
        $this->authorizedUserId = Auth::id();
    }

    /**
     * @param User $user
     * 
     * @return JsonResource
     */
    public function subscribtions(User $user): JsonResource
    {
        $usersIds = $this->userSubscribtionRepository->getSubscribtionsIds($user->id);
        $usersData = $this->userRepository->getUsersData($usersIds);

        return ShortUserResource::collection($usersData);
    }

    /**
     * @param User $user
     * 
     * @return JsonResource
     */
    public function subscribers(User $user): JsonResource
    {
        $usersIds = $this->userSubscribtionRepository->getSubscribersIds($user->id);
        $usersData = $this->userRepository->getUsersData($usersIds);

        return ShortUserResource::collection($usersData);
    }

    /**
     * @param User $user
     * 
     * @return Response
     */
    public function add(User $user): Response
    {
        return $this->userSubscribtionRepository->create($user->id, $this->authorizedUserId);
    }

    /**
     * @param User $user
     * 
     * @return Response
     */
    public function remove(User $user): Response
    {
        // Отключаем подписку на уведомления о новых твитах (независимо от её наличия всё отработает нормально)
        $this->notificationsSubscribtionService->unsubscribe($user);
        // После чего отписываемся уже от самого пользователя
        return $this->userSubscribtionRepository->remove($user->id, $this->authorizedUserId);
    }
}
