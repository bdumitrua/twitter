<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\DTO\UserGroupDTO;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UsersListRepository
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

    protected function baseQuery(): Builder
    {
        return $this->userGroup->newQuery();
    }

    protected function baseQueryWithRelations(array $relations = []): Builder
    {
        return $this->baseQuery()->with($relations);
    }

    protected function queryById(int $id, array $relations = []): Builder
    {
        return $this->baseQueryWithRelations($relations)->where('id', '=', $id);
    }

    protected function queryByUserId(int $id, array $relations = []): Builder
    {
        return $this->baseQueryWithRelations($relations)->where(USER_ID, '=', $id);
    }

    protected function queryByBothIds(int $userGroupId, int $userId): Builder
    {
        return $this->userGroupMember->newQuery()
            ->where(USER_GROUP_ID, '=', $userGroupId)
            ->where(USER_ID, '=', $userId);
    }

    protected function userInGroupExist(int $userGroupId, int $userId): bool
    {
        return $this->queryByBothIds($userGroupId, $userId)->exists();
    }

    public function getById(int $id, array $relations = []): UserGroup
    {
        return $this->queryById($id, $relations)->first() ?? new UserGroup();
    }

    public function getByUserId(int $userId, array $relations = []): Collection
    {
        return $this->queryByUserId($userId, $relations)->get() ?? new Collection();
    }

    public function create(int $userId, UserGroupDTO $dto): void
    {
        $this->userGroup->create([
            USER_ID => $userId,
            NAME => $dto->name,
            DESCRIPTION => $dto->description
        ]);
    }

    public function update(UserGroup $userGroup, UserGroupDTO $dto): void
    {
        $userGroup->update([
            NAME => $dto->name,
            DESCRIPTION => $dto->description
        ]);
    }

    public function delete(UserGroup $userGroup): void
    {
        $userGroup->delete();
    }

    public function addUser(int $userGroupId, int $userId): void
    {
        if (empty($this->userInGroupExist($userGroupId, $userId))) {
            $this->userGroupMember->create([
                USER_GROUP_ID => $userGroupId,
                USER_ID => $userId
            ]);
        }
    }

    public function removeUser(int $userGroupId, int $userId): void
    {
        /** @var UserGroupMember */
        $subscribtion = $this->queryByBothIds($userGroupId, $userId)->first();

        if ($subscribtion) {
            $subscribtion->delete();
        }
    }
}