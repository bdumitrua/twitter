<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\DTO\UserGroupDTO;
use App\Modules\User\Events\UserGroupMembersUpdateEvent;
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

    protected function queryByBothIds(int $userGroupId, int $userId): Builder
    {
        return $this->userGroupMember->newQuery()
            ->where('user_group_id', '=', $userGroupId)
            ->where('user_id', '=', $userId);
    }

    public function getById(int $id, array $relations = []): UserGroup
    {
        return $this->userGroup->with($relations)
            ->where('id', '=', $id)
            ->first() ?? new UserGroup();
    }

    public function getByUserId(int $userId, array $relations = []): Collection
    {
        return $this->userGroup->with($relations)
            ->where('user_id', '=', $userId)
            ->get();
    }

    public function create(UserGroupDTO $dto, int $userId): void
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
            'name' => $dto->name ?? $userGroup->name,
            'description' => $dto->description ?? $userGroup->description
        ]);
    }

    public function delete(UserGroup $userGroup): void
    {
        $userGroup->delete();
    }

    public function addUser(int $userGroupId, int $userId): void
    {
        if (empty($this->queryByBothIds($userGroupId, $userId)->exists())) {
            $userGroupMember = $this->userGroupMember->create([
                'user_group_id' => $userGroupId,
                'user_id' => $userId
            ]);
            event(new UserGroupMembersUpdateEvent($userGroupMember, true));
        }
    }

    public function removeUser(int $userGroupId, int $userId): void
    {
        /** @var UserGroupMember */
        $userGroupMember = $this->queryByBothIds($userGroupId, $userId)->first();

        if ($userGroupMember) {
            event(new UserGroupMembersUpdateEvent($userGroupMember, false));
            $userGroupMember->delete();
        }
    }
}
