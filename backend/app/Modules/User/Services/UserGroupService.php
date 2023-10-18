<?php

namespace App\Modules\User\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Repositories\UserGroupRepository;
use Illuminate\Support\Facades\Auth;

class UserGroupService
{
    protected $userGroupRepository;

    public function __construct(
        UserGroupRepository $userGroupRepository,
    ) {
        $this->userGroupRepository = $userGroupRepository;
    }

    public function index()
    {
        return $this->userGroupRepository->getById(Auth::id());
    }
    public function create(Request $request)
    {
        return $this->userGroupRepository->create(Auth::id(), $request);
    }
    public function update(UserGroup $userGroup, Request $request)
    {
        return $this->userGroupRepository->update($userGroup, $request);
    }
    public function destroy(UserGroup $userGroup)
    {
        return $this->userGroupRepository->delete($userGroup);
    }
    public function add(UserGroup $userGroup, User $user)
    {
        return $this->userGroupRepository->addUser($userGroup->id, $user->id);
    }
    public function remove(UserGroup $userGroup, User $user)
    {
        return $this->userGroupRepository->removeUser($userGroup->id, $user->id);
    }
}
