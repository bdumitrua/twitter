<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserSubscribtionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserSubscribtionService
{
    protected UserSubscribtionRepository $userSubscribtionRepository;
    protected ?int $authorizedUserId;

    public function __construct(
        UserSubscribtionRepository $userSubscribtionRepository,
    ) {
        $this->userSubscribtionRepository = $userSubscribtionRepository;
        $this->authorizedUserId = Auth::id();
    }

    /**
     * @param User $user
     * 
     * @return Collection
     */
    public function subscribtions(User $user): Collection
    {
        return $this->userSubscribtionRepository->getSubscribtions($user->id);
    }

    /**
     * @param User $user
     * 
     * @return Collection
     */
    public function subscribers(User $user): Collection
    {
        return $this->userSubscribtionRepository->getSubscribers($user->id);
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
        return $this->userSubscribtionRepository->remove($user->id, $this->authorizedUserId);
    }
}
