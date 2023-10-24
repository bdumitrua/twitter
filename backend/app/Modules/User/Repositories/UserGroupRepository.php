<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\DTO\UserGroupDTO;
use App\Modules\User\Events\UserGroupMembersUpdateEvent;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class UserGroupRepository
{
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

    protected function queryByUserId(int $userId, array $relations = []): Builder
    {
        return $this->userGroup->newQuery()
            ->with($relations)
            ->where('user_id', '=', $userId);
    }

    public function getById(int $id, array $relations = []): UserGroup
    {
        return $this->userGroup->with($relations)
            ->where('id', '=', $id)
            ->first() ?? new UserGroup();
    }

    public function getByUserId(int $userId, array $relations = [], bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_GROUPS . $userId . KEY_WITH_RELATIONS . implode(',', $relations);

        if ($updateCache) {
            $userGroups = $this->queryByUserId($userId, $relations)->get();
            Cache::forever($cacheKey, $userGroups);
        }

        return Cache::rememberForever($cacheKey, function () use ($userId, $relations) {
            return $this->queryByUserId($userId, $relations)->get();
        });
    }

    public function create(UserGroupDTO $dto, int $userId): void
    {
        $createdUserGroup = $this->userGroup->create([
            'user_id' => $userId,
            'name' => $dto->name,
            'description' => $dto->description
        ]);

        if (!empty($createdUserGroup)) {
            $this->recacheUserGroups($userId);
        }
    }

    public function update(UserGroup $userGroup, UserGroupDTO $dto): void
    {
        $updatingStatus = $userGroup->update([
            'name' => $dto->name ?? $userGroup->name,
            'description' => $dto->description ?? $userGroup->description
        ]);

        if (!empty($updatingStatus)) {
            $this->recacheUserGroups($userGroup->user_id);
        }
    }

    public function delete(UserGroup $userGroup): void
    {
        $deletingStatus = $userGroup->delete() ?? false;

        if (!empty($deletingStatus)) {
            $this->recacheUserGroups($userGroup->user_id);
        }
    }

    public function addUser(int $userGroupId, int $userId): void
    {
        if (empty($this->queryByBothIds($userGroupId, $userId)->exists())) {
            $this->userGroupMember->create([
                'user_group_id' => $userGroupId,
                'user_id' => $userId
            ]);
            event(new UserGroupMembersUpdateEvent($userGroupId, true));
        }
    }

    public function removeUser(int $userGroupId, int $userId): void
    {
        /** @var UserGroupMember */
        $userGroupMember = $this->queryByBothIds($userGroupId, $userId)->first();

        if ($userGroupMember) {
            $userGroupId = $userGroupMember->user_group_id;
            $userGroupMember->delete();
            event(new UserGroupMembersUpdateEvent($userGroupId, false));
        }
    }

    private function recacheUserGroups(int $userId)
    {
        $this->getByUserId($userId, [], true);
    }
}
