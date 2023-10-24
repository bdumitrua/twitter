<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Services\UserSubscribtionService;
use Illuminate\Http\JsonResponse;

class UserSubscribtionController extends Controller
{
    private $userSubscribtionService;

    public function __construct(UserSubscribtionService $userSubscribtionService)
    {
        $this->userSubscribtionService = $userSubscribtionService;
    }

    public function subscribtions(User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userSubscribtionService->subscribtions($user);
        });
    }
    public function subscribers(User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userSubscribtionService->subscribers($user);
        });
    }
    public function add(User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userSubscribtionService->add($user);
        });
    }
    public function remove(User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userSubscribtionService->remove($user);
        });
    }
}
