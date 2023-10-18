<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\DTO\UserGroupDTO;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    protected function baseQuery(): Builder
    {
        return $this->userGroup->newQuery();
    }

    protected function baseQueryWithRelations(array $relations = []): Builder
    {
        return $this->baseQuery()->with($relations);
    }

    protected function queryById(int $id): Builder
    {
        return $this->baseQuery()->where('id', '=', $id);
    }

    protected function queryByUserId(int $id): Builder
    {
        return $this->baseQuery()->where('user_id', '=', $id);
    }

    protected function queryByBothIds(int $userGroupId, int $userId): Builder
    {
        return $this->userGroupMember->newQuery()
            ->where('user_group_id', '=', $userGroupId)
            ->where('user_id', '=', $userId);
    }

    protected function userInGroupExist(int $userGroupId, int $userId): bool
    {
        return $this->queryByBothIds($userGroupId, $userId)->exists();
    }

    public function getById(int $id): UserGroup
    {
        return $this->queryById($id)->first() ?? new UserGroup();
    }

    public function getByUserId(int $userId): UserGroup
    {
        return $this->queryByUserId($userId)->first() ?? new UserGroup();
    }

    public function create(int $userId, UserGroupDTO $dto): void
    {
        $this->userGroup->create([
            'user_id' => $userId,
            'name' => $dto->name,
            'description' => $dto->description
        ]);
    }

    public function update(UserGroup $userGroup, UserGroupDTO $dto): void
    {
        $userGroup->update([
            'name' => $dto->name,
            'description' => $dto->description
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
                'user_group_id' => $userGroupId,
                'user_id' => $userId
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
