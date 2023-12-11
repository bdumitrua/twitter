<?php

namespace App\Modules\User\Repositories;

use App\Exceptions\NotFoundException;
use App\Modules\User\DTO\UserGroupDTO;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserGroupRepository
{
    use GetCachedData;

    protected UserGroup $userGroup;
    protected UserGroupMember $userGroupMember;

    public function __construct(
        UserGroup $userGroup,
        UserGroupMember $userGroupMember
    ) {
        $this->userGroup = $userGroup;
        $this->userGroupMember = $userGroupMember;
    }

    /**
     * @param int $userGroupId
     * @param int $userId
     * 
     * @return Builder
     */
    protected function queryByBothIds(int $userGroupId, int $userId): Builder
    {
        return $this->userGroupMember->newQuery()
            ->where('user_group_id', '=', $userGroupId)
            ->where('user_id', '=', $userId);
    }

    /**
     * @param int $userId
     * 
     * @return Builder
     */
    protected function queryByUserId(int $userId): Builder
    {
        return $this->userGroup->newQuery()
            ->where('user_id', '=', $userId);
    }

    /**
     * @param int $id
     * 
     * @return UserGroup
     */
    public function getById(int $id): UserGroup
    {
        $userGroup = $this->userGroup
            ->withCount(['members'])
            ->with(['members_data'])
            ->where('id', '=', $id)
            ->first();

        if (empty($userGroup)) {
            throw new NotFoundException('Group');
        }

        return $userGroup;
    }

    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return Collection
     */
    public function getByUserId(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_GROUPS . $userId;
        return $this->getCachedData($cacheKey, null, function () use ($userId) {
            return $this->queryByUserId($userId)->get();
        }, $updateCache);
    }

    /**
     * @param UserGroupDTO $dto
     * @param int $userId
     * 
     * @return void
     */
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

    /**
     * @param UserGroup $userGroup
     * @param UserGroupDTO $dto
     * 
     * @return void
     */
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

    /**
     * @param UserGroup $userGroup
     * 
     * @return void
     */
    public function delete(UserGroup $userGroup): void
    {
        $deletingStatus = $userGroup->delete();

        if ($deletingStatus) {
            $this->clearUserGroupsCache($userGroup->user_id);
        }
    }

    /**
     * @param int $userGroupId
     * @param int $userId
     * 
     * @return void
     */
    public function addUser(int $userGroupId, int $userId): void
    {
        if (empty($this->queryByBothIds($userGroupId, $userId)->exists())) {
            $this->userGroupMember->create([
                'user_group_id' => $userGroupId,
                'user_id' => $userId
            ]);
        }
    }

    /**
     * @param int $userGroupId
     * @param int $userId
     * 
     * @return void
     */
    public function removeUser(int $userGroupId, int $userId): void
    {
        if ($userGroupMember = $this->queryByBothIds($userGroupId, $userId)->first()) {
            $userGroupMember->delete();
        }
    }

    /**
     * @param int $userId
     * 
     * @return void
     */
    private function clearUserGroupsCache(int $userId): void
    {
        $cacheKey = KEY_USER_GROUPS . (string)$userId;
        $this->clearCache($cacheKey);
    }
}
