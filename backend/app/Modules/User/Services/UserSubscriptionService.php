<?php

namespace App\Modules\User\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserSubscribtionRepository;
use Illuminate\Support\Facades\Auth;

class UserSubscriptionService
{
    protected $userSubscribtionRepository;

    public function __construct(
        UserSubscribtionRepository $userSubscribtionRepository,
    ) {
        $this->userSubscribtionRepository = $userSubscribtionRepository;
    }

    public function subscriptions(User $user)
    {
        return $this->userSubscribtionRepository->getSubscriptions($user->id);
    }

    public function subscribers(User $user)
    {
        return $this->userSubscribtionRepository->getSubscribers($user->id);
    }

    public function add(User $user)
    {
        return $this->userSubscribtionRepository->create(Auth::id(), $user->id);
    }

    public function remove(User $user)
    {
        $this->userSubscribtionRepository->remove(Auth::id(), $user->id);
    }
}
