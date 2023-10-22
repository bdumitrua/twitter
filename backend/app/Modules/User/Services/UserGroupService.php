<?php

namespace App\Modules\User\Services;

use App\Modules\User\DTO\UserGroupDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Repositories\UserGroupRepository;
use App\Modules\User\Requests\CreateUserGroupRequest;
use App\Modules\User\Requests\UpdateUserGroupRequest;
use App\Modules\User\Requests\UserGroupRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserGroupService
{
    protected $userGroupRepository;

    public function __construct(
        UserGroupRepository $userGroupRepository,
    ) {
        $this->userGroupRepository = $userGroupRepository;
    }

    public function index(): Collection
    {
        return $this->userGroupRepository->getByUserId(
            Auth::id(),
            ['members']
        );
    }

    public function create(CreateUserGroupRequest $createUserGroupRequest): void
    {
        $userGroupDTO = $this->createDTO($createUserGroupRequest);

        $this->userGroupRepository->create($userGroupDTO, Auth::id());
    }

    public function update(UserGroup $userGroup, UpdateUserGroupRequest $updateUserGroupRequest): void
    {
        $userGroupDTO = $this->createDTO($updateUserGroupRequest);

        $this->userGroupRepository->update($userGroup, $userGroupDTO);
    }

    public function destroy(UserGroup $userGroup): void
    {
        $this->userGroupRepository->delete($userGroup);
    }

    public function add(UserGroup $userGroup, User $user): void
    {
        $this->userGroupRepository->addUser($userGroup->id, $user->id);
    }

    public function remove(UserGroup $userGroup, User $user): void
    {
        $this->userGroupRepository->removeUser($userGroup->id, $user->id);
    }

    protected function createDTO(Request $userGroupRequest): UserGroupDTO
    {
        $filteredRequestData = array_filter($userGroupRequest->all());
        if (empty($filteredRequestData)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'At least one field must be filled');
        }

        $userGroupDTO = new UserGroupDTO();
        foreach ($filteredRequestData as $key => $value) {
            $userGroupDTO->$key = $value;
        }

        return $userGroupDTO;
    }
}
