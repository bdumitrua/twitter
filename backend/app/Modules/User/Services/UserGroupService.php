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
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserGroupService
{
    use CreateDTO;

    protected $userGroupRepository;

    public function __construct(
        UserGroupRepository $userGroupRepository,
    ) {
        $this->userGroupRepository = $userGroupRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return Cache::rememberForever(KEY_USER_GROUPS . $authorizedUserId, function () use ($authorizedUserId) {
            return $this->userGroupRepository->getByUserId($authorizedUserId);
        });
    }

    public function create(CreateUserGroupRequest $createUserGroupRequest): void
    {
        $authorizedUserId = Auth::id();
        $userGroupDTO = $this->createDTO($createUserGroupRequest, UserGroupDTO::class);

        $createdUserGroup = $this->userGroupRepository->create($userGroupDTO, $authorizedUserId);

        if (!empty($createdUserGroup)) {
            $this->cacheGroupsForever($authorizedUserId);
        }
    }

    public function update(UserGroup $userGroup, UpdateUserGroupRequest $updateUserGroupRequest): void
    {
        $userGroupDTO = $this->createDTO($updateUserGroupRequest, UserGroupDTO::class);

        $userGroupUpdateStatus = $this->userGroupRepository->update($userGroup, $userGroupDTO);

        if (!empty($userGroupUpdateStatus)) {
            $this->cacheGroupsForever($userGroup->user_id);
        }
    }

    public function destroy(UserGroup $userGroup): void
    {
        $userGroupDeleteStatus = $this->userGroupRepository->delete($userGroup);

        if (!empty($userGroupDeleteStatus)) {
            $this->cacheGroupsForever($userGroup->user_id);
        }
    }

    public function add(UserGroup $userGroup, User $user): void
    {
        $this->userGroupRepository->addUser($userGroup->id, $user->id);
    }

    public function remove(UserGroup $userGroup, User $user): void
    {
        $this->userGroupRepository->removeUser($userGroup->id, $user->id);
    }

    private function cacheGroupsForever(int $userId)
    {
        Cache::forever(KEY_USER_GROUPS . $userId, function () use ($userId) {
            return $this->userGroupRepository->getByUserId($userId);
        });
    }
}
