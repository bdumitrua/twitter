<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
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
    public function create(Request $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->userGroupService->create($request);
        });
    }
    public function update(UserGroup $userGroup, Request $request)
    {
        return $this->handleServiceCall(function () use ($userGroup, $request) {
            return $this->userGroupService->update($userGroup, $request);
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
