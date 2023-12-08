<?php

namespace App\Modules\User\Repositories;

use App\Exceptions\NotFoundException;
use App\Modules\User\DTO\UserGroupDTO;
use App\Modules\User\Events\UserGroupMembersUpdateEvent;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserGroupRepository
{
    use GetCachedData;

    protected $userGroup;
    protected $userGroupMember;

    public function __construct(
        UserGroup $userGroup,
        UserGroupMember $userGroupMember
    ) {
        $this->userGroup = $userGroup;
        $this->userGroupMember = $userGroupMember;
    }

    protected function queryByBothIds(int $userGroupId, int $userId): Builder
    {
        return $this->userGroupMember->newQuery()
            ->where('user_group_id', '=', $userGroupId)
            ->where('user_id', '=', $userId);
    }

    protected function queryByUserId(int $userId): Builder
    {
        return $this->userGroup->newQuery()
            ->where('user_id', '=', $userId);
    }

    public function getById(int $id): UserGroup
    {
        return $this->userGroup
            ->withCount(['members'])
            ->with(['members_data'])
            ->where('id', '=', $id)
            ->first();
    }

    public function getByUserId(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_GROUPS . $userId;
        return $this->getCachedData($cacheKey, null, function () use ($userId) {
            return $this->queryByUserId($userId)->get();
        }, $updateCache);
    }

    public function create(UserGroupDTO $dto, int $userId): void
    {
        $createdUserGroup = $this->userGroup->create([
            'user_id' => $userId,
            'name' => $dto->name,
            'description' => $dto->description
        ]);

        if (!empty($createdUserGroup)) {
            $this->clearUserGroupsCache($userId);
        }
    }

    public function update(UserGroup $userGroup, UserGroupDTO $dto): void
    {
        $updatingStatus = $userGroup->update([
            'name' => $dto->name ?? $userGroup->name,
            'description' => $dto->description ?? $userGroup->description
        ]);

        if (!empty($updatingStatus)) {
            $this->clearUserGroupsCache($userGroup->user_id);
        }
    }

    public function delete(UserGroup $userGroup): void
    {
        $deletingStatus = $userGroup->delete();

        if ($deletingStatus) {
            $this->clearUserGroupsCache($userGroup->user_id);
        }
    }

    public function addUser(int $userGroupId, int $userId): void
    {
        if (empty($this->queryByBothIds($userGroupId, $userId)->exists())) {
            $this->userGroupMember->create([
                'user_group_id' => $userGroupId,
                'user_id' => $userId
            ]);
        }
    }

    public function removeUser(int $userGroupId, int $userId): void
    {
        if ($userGroupMember = $this->queryByBothIds($userGroupId, $userId)->first()) {
            $userGroupMember->delete();
        }
    }

    private function clearUserGroupsCache(int $userId): void
    {
        $cacheKey = KEY_USER_GROUPS . (string)$userId;
        $this->clearCache($cacheKey);
    }
}
