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
    public function create()
    {
        return $this->handleServiceCall(function () {
            return $this->userGroupService->create();
        });
    }
    public function update(UserGroup $userGroup)
    {
        return $this->handleServiceCall(function () use ($userGroup) {
            return $this->userGroupService->update($userGroup);
        });
    }
    public function destroy(UserGroup $userGroup)
    {
        return $this->handleServiceCall(function () use ($userGroup) {
            return $this->userGroupService->destroy($userGroup);
        });
    }
    public function add(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userGroupService->add($user);
        });
    }
    public function remove(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userGroupService->remove($user);
        });
    }
}
