<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\DTO\UsersListDTO;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UsersListRepository
{
    protected $usersList;
    protected $usersListMember;

    public function __construct(
        UsersList $usersList,
        UsersListMember $usersListMember,
    ) {
        $this->usersList = $usersList;
        $this->usersListMember = $usersListMember;
    }

    protected function baseQuery(): Builder
    {
        return $this->usersList->newQuery();
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

    protected function queryUserMembership(int $usersListId, int $userId): Builder
    {
        return $this->usersListMember->newQuery()
            ->where('users_list_id', '=', $usersListId)
            ->where(USER_ID, '=', $userId);
    }

    protected function userIsListMember(int $usersListId, int $userId): bool
    {
        return $this->queryUserMembership($usersListId, $userId)->exists();
    }

    public function getById(int $id, array $relations = []): UsersList
    {
        return $this->queryById($id, $relations)->first() ?? new UsersList();
    }

    public function getByUserId(int $userId, array $relations = []): Collection
    {
        return $this->queryByUserId($userId, $relations)->get() ?? new Collection();
    }

    public function create(int $userId, UsersListDTO $dto): void
    {
        $this->usersList->create([
            USER_ID => $userId,
            NAME => $dto->name,
            DESCRIPTION => $dto->description,
            'bg_image' => $dto->bgImage,
            'is_private' => $dto->isPrivate,
            'subsribers_count' => 0,
            'members_count' => 0,
        ]);
    }

    public function update(UsersList $usersList, UsersListDTO $dto): void
    {
        $usersList->update([
            NAME => $dto->name,
            DESCRIPTION => $dto->description,
            'bg_image' => $dto->bgImage,
            'is_private' => $dto->isPrivate,
        ]);
    }

    public function delete(UsersList $usersList): void
    {
        $usersList->delete();
    }

    public function addMember(int $usersListId, int $userId): void
    {
        if (empty($this->userIsListMember($usersListId, $userId))) {
            $this->usersListMember->create([
                USER_GROUP_ID => $usersListId,
                USER_ID => $userId
            ]);
        }
    }

    public function removeMember(int $usersListId, int $userId): void
    {
        /** @var UsersListMember */
        $membership = $this->queryUserMembership($usersListId, $userId)->first();

        if ($membership) {
            $membership->delete();
        }
    }
}
