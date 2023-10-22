<?php

namespace App\Modules\User\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserSubscribtionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserSubscriptionService
{
    protected $userSubscribtionRepository;

    public function __construct(
        UserSubscribtionRepository $userSubscribtionRepository,
    ) {
        $this->userSubscribtionRepository = $userSubscribtionRepository;
    }

    public function subscriptions(User $user): Collection
    {
        return $this->userSubscribtionRepository->getSubscriptions($user->id);
    }

    public function subscribers(User $user): Collection
    {
        return $this->userSubscribtionRepository->getSubscribers($user->id);
    }

    public function add(User $user): void
    {
        $this->userSubscribtionRepository->create($user->id, Auth::id());
    }

    public function remove(User $user): void
    {
        $this->userSubscribtionRepository->remove($user->id, Auth::id());
    }
}
