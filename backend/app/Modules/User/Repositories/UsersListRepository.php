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
        return $this->baseQueryWithRelations($relations)->where('user_id', '=', $id);
    }

    protected function queryUserMembership(int $usersListId, int $userId): Builder
    {
        return $this->usersListMember->newQuery()
            ->where('users_list_id', '=', $usersListId)
            ->where('user_id', '=', $userId);
    }

    protected function userIsListMember(int $usersListId, int $userId): bool
    {
        return $this->queryUserMembership($usersListId, $userId)->exists();
    }

    protected function queryUserSubscribtion(int $usersListId, int $userId): Builder
    {
        return $this->usersListMember->newQuery()
            ->where('users_list_id', '=', $usersListId)
            ->where('user_id', '=', $userId);
    }

    protected function userIsListSubscriber(int $usersListId, int $userId): bool
    {
        return $this->queryUserSubscribtion($usersListId, $userId)->exists();
    }

    public function getById(int $id, array $relations = []): UsersList
    {
        return $this->queryById($id, $relations)->first() ?? new UsersList();
    }

    public function getByUserId(int $userId, array $relations = []): Collection
    {
        return $this->queryByUserId($userId, $relations)->get() ?? new Collection();
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
        if (empty($this->userIsListMember($usersListId, $userId))) {
            $usersListMember = $this->usersListMember->create([
                'users_list_id' => $usersListId,
                'user_id' => $userId
            ]);

            event(new UsersListMembersUpdateEvent($usersListMember, true));
        }
    }

    public function removeMember(int $usersListId, int $userId): void
    {
        /** @var UsersListMember */
        $usersListMember = $this->queryUserMembership($usersListId, $userId)->first();

        if ($usersListMember) {
            event(new UsersListMembersUpdateEvent($usersListMember, false));
            $usersListMember->delete();
        }
    }

    public function subscribe(int $usersListId, int $userId): void
    {
        if (empty($this->userIsListSubscriber($usersListId, $userId))) {
            $usersListSubscribtion = $this->usersListSubscribtion->create([
                'users_list_id' => $usersListId,
                'user_id' => $userId
            ]);

            event(new UsersListSubscribtionEvent($usersListSubscribtion, true));
        }
    }

    public function unsubscribe(int $usersListId, int $userId): void
    {
        /** @var UsersListSubscribtion */
        $usersListSubscribtion = $this->queryUserSubscribtion($usersListId, $userId)->first();

        if ($usersListSubscribtion) {
            event(new UsersListSubscribtionEvent($usersListSubscribtion, false));
            $usersListSubscribtion->delete();
        }
    }
}
