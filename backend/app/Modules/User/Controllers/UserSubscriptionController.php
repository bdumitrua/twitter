<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Services\UserSubscriptionService;

class UserSubscribtionController extends Controller
{
    private $userSubscriptionService;

    public function __construct(UserSubscriptionService $userSubscriptionService)
    {
        $this->userSubscriptionService = $userSubscriptionService;
    }

    public function subscriptions(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userSubscriptionService->subscriptions($user);
        });
    }
    public function subscribers(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userSubscriptionService->subscribers($user);
        });
    }
    public function add(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userSubscriptionService->add($user);
        });
    }
    public function remove(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userSubscriptionService->remove($user);
        });
    }
}
