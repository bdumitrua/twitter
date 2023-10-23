<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\DTO\UsersListDTO;
use App\Modules\User\Events\UsersListMembersUpdateEvent;
use App\Modules\User\Events\UsersListSubscribtionEvent;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListMember;
use App\Modules\User\Models\UsersListSubscribtion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UsersListRepository
{
    protected $usersList;
    protected $usersListMember;
    protected $usersListSubscribtion;

    public function __construct(
        UsersList $usersList,
        UsersListMember $usersListMember,
        UsersListSubscribtion $usersListSubscribtion,
    ) {
        $this->usersList = $usersList;
        $this->usersListMember = $usersListMember;
        $this->usersListSubscribtion = $usersListSubscribtion;
    }

    protected function queryUserMembership(int $usersListId, int $userId): Builder
    {
        return $this->usersListMember->newQuery()
            ->where('users_list_id', '=', $usersListId)
            ->where('user_id', '=', $userId);
    }

    protected function queryUserSubscribtion(int $usersListId, int $userId): Builder
    {
        return $this->usersListMember->newQuery()
            ->where('users_list_id', '=', $usersListId)
            ->where('user_id', '=', $userId);
    }

    public function getById(int $id, array $relations = []): UsersList
    {
        return $this->usersList->with($relations)
            ->where('id', '=', $id)
            ->first() ?? new UsersList();
    }

    public function getByUserId(int $userId, array $relations = []): Collection
    {
        return $this->usersList->with($relations)
            ->where('user_id', '=', $userId)
            ->get();
    }

    public function create(UsersListDTO $dto, int $userId): void
    {
        $this->usersList->create([
            'user_id' => $userId,
            'name' => $dto->name,
            'description' => $dto->description,
            'bg_image' => $dto->bgImage,
            'is_private' => $dto->isPrivate,
            'subsribers_count' => 0,
            'members_count' => 0,
        ]);
    }

    public function update(UsersList $usersList, UsersListDTO $dto): void
    {
        $usersList->update([
            'name' => $dto->name ?? $usersList->name,
            'description' => $dto->description ?? $usersList->description,
            'is_private' => $dto->isPrivate ?? $usersList->isPrivate,
            // TODO FILES
            'bg_image' => $dto->bgImage ?? $usersList->bgImage,
        ]);
    }

    public function delete(UsersList $usersList): void
    {
        $usersList->delete();
    }

    public function addMember(int $usersListId, int $userId): void
    {
        if (empty($this->queryUserMembership($usersListId, $userId)->exists())) {
            $usersListMember = $this->usersListMember->create([
                'users_list_id' => $usersListId,
                'user_id' => $userId
            ]);

            event(new UsersListMembersUpdateEvent($usersListMember, true));
        }
    }

    public function removeMember(int $usersListId, int $userId): void
    {
        if (!empty($usersListMember = $this->queryUserMembership($usersListId, $userId)->first())) {
            event(new UsersListMembersUpdateEvent($usersListMember, false));
            $usersListMember->delete();
        }
    }

    public function subscribe(int $usersListId, int $userId): void
    {
        if (empty($this->queryUserSubscribtion($usersListId, $userId)->exists())) {
            $usersListSubscribtion = $this->usersListSubscribtion->create([
                'users_list_id' => $usersListId,
                'user_id' => $userId
            ]);

            event(new UsersListSubscribtionEvent($usersListSubscribtion, true));
        }
    }

    public function unsubscribe(int $usersListId, int $userId): void
    {
        if (!empty($usersListSubscribtion = $this->queryUserSubscribtion($usersListId, $userId)->first())) {
            event(new UsersListSubscribtionEvent($usersListSubscribtion, false));
            $usersListSubscribtion->delete();
        }
    }
}
