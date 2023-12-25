<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Requests\CreateUserGroupRequest;
use App\Modules\User\Requests\UpdateUserGroupRequest;
use App\Modules\User\Services\UserGroupService;
use Illuminate\Http\JsonResponse;

class UserGroupController extends Controller
{
    private $userGroupService;

    public function __construct(UserGroupService $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    public function index(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->userGroupService->index();
        });
    }
    public function show(UserGroup $userGroup): JsonResponse
    {
        return $this->handleServiceCall(function () use ($userGroup) {
            return $this->userGroupService->show($userGroup);
        });
    }
    public function create(CreateUserGroupRequest $createUserGroupRequest): JsonResponse
    {
        return $this->handleServiceCall(function () use ($createUserGroupRequest) {
            return $this->userGroupService->create($createUserGroupRequest);
        });
    }
    public function update(UserGroup $userGroup, UpdateUserGroupRequest $updateUserGroupRequest): JsonResponse
    {
        return $this->handleServiceCall(function () use ($userGroup, $updateUserGroupRequest) {
            return $this->userGroupService->update($userGroup, $updateUserGroupRequest);
        });
    }
    public function delete(UserGroup $userGroup, Request $request): JsonResponse
    {
        return $this->handleServiceCall(function () use ($userGroup, $request) {
            return $this->userGroupService->delete($userGroup, $request);
        });
    }
    public function add(UserGroup $userGroup, User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($userGroup, $user) {
            return $this->userGroupService->add($userGroup, $user);
        });
    }
    public function remove(UserGroup $userGroup, User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($userGroup, $user) {
            return $this->userGroupService->remove($userGroup, $user);
        });
    }
}
