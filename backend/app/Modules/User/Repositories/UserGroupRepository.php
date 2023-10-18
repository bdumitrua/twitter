<?php

namespace App\Modules\User\Repositories;

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

    public function getById(int $id): ?UserGroup
    {
        return $this->queryById($id)->first();
    }

    public function create(int $userId, $data): void
    {
        $this->userGroup->create([
            'user_id' => $userId,
            'name' => $data->name,
            'description' => $data->description
        ]);
    }

    public function update(UserGroup $userGroup, $data): void
    {
        $userGroup->update([
            'name' => $data->name,
            'description' => $data->description
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
