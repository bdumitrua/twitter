<?php

namespace App\Modules\User\Services;

use App\Modules\User\DTO\UserGroupDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Repositories\UserGroupRepository;
use App\Modules\User\Requests\UserGroupRequest;
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
        return $this->userGroupRepository->getByUserId(Auth::id());
    }
    public function create(UserGroupRequest $userGroupRequest)
    {
        $userGroupDTO = $this->createDTO($userGroupRequest);

        return $this->userGroupRepository->create(Auth::id(), $userGroupDTO);
    }
    public function update(UserGroup $userGroup, UserGroupRequest $userGroupRequest)
    {
        $userGroupDTO = $this->createDTO($userGroupRequest);

        return $this->userGroupRepository->update($userGroup, $userGroupDTO);
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

    protected function createDTO(UserGroupRequest $userGroupRequest): UserGroupDTO
    {
        return new UserGroupDTO(
            $userGroupRequest->name,
            $userGroupRequest->description,
        );
    }
}
