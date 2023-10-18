<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Requests\UserGroupRequest;
use App\Modules\User\Services\UserGroupService;

class UserGroupController extends Controller
{
    private $userGroupService;

    public function __construct(UserGroupService $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    public function index()
    {
        return $this->handleServiceCall(function () {
            return $this->userGroupService->index();
        });
    }
    public function create(UserGroupRequest $userGroupRequest)
    {
        return $this->handleServiceCall(function () use ($userGroupRequest) {
            return $this->userGroupService->create($userGroupRequest);
        });
    }
    public function update(UserGroup $userGroup, UserGroupRequest $userGroupRequest)
    {
        return $this->handleServiceCall(function () use ($userGroup, $userGroupRequest) {
            return $this->userGroupService->update($userGroup, $userGroupRequest);
        });
    }
    public function destroy(UserGroup $userGroup)
    {
        return $this->handleServiceCall(function () use ($userGroup) {
            return $this->userGroupService->destroy($userGroup);
        });
    }
    public function add(UserGroup $userGroup, User $user)
    {
        return $this->handleServiceCall(function () use ($userGroup, $user) {
            return $this->userGroupService->add($userGroup, $user);
        });
    }
    public function remove(UserGroup $userGroup, User $user)
    {
        return $this->handleServiceCall(function () use ($userGroup, $user) {
            return $this->userGroupService->remove($userGroup, $user);
        });
    }
}
