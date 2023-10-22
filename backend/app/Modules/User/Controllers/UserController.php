<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Requests\SearchRequest;
use App\Modules\User\Requests\UserUpdateRequest;
use App\Modules\User\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->userService->index();
        });
    }

    public function show(User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userService->show($user);
        });
    }

    public function update(UserUpdateRequest $userUpdateRequest): JsonResponse
    {
        return $this->handleServiceCall(function () use ($userUpdateRequest) {
            return $this->userService->update($userUpdateRequest);
        });
    }

    public function search(SearchRequest $request): JsonResponse
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->userService->search($request);
        });
    }
}
