<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserSubscribtionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserSubscribtionService
{
    protected $userSubscribtionRepository;

    public function __construct(
        UserSubscribtionRepository $userSubscribtionRepository,
    ) {
        $this->userSubscribtionRepository = $userSubscribtionRepository;
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
     * @return void
     */
    public function add(User $user): void
    {
        $this->userSubscribtionRepository->create($user->id, Auth::id());
    }

    /**
     * @param User $user
     * 
     * @return void
     */
    public function remove(User $user): void
    {
        $this->userSubscribtionRepository->remove($user->id, Auth::id());
    }
}
