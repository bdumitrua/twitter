<?php

namespace App\Modules\User\Repositories;

use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
use App\Helpers\TimeHelper;
use App\Modules\User\DTO\UsersListDTO;
use App\Modules\User\Events\DeletedUsersListEvent;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListMember;
use App\Modules\User\Models\UsersListSubscribtion;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UsersListRepository
{
    use GetCachedData;

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
        return $this->usersListSubscribtion->newQuery()
            ->where('users_list_id', '=', $usersListId)
            ->where('user_id', '=', $userId);
    }

    protected function getUserListsIds(int $userId): array
    {
        $whereIsCreator = $this->usersList
            ->where('user_id', '=', $userId)
            ->get(['id'])
            ->pluck('id')
            ->toArray();

        $whereIsSubscriber = $this->usersListSubscribtion
            ->where('user_id', '=', $userId)
            ->get(['users_list_id'])
            ->pluck('users_list_id')
            ->toArray();

        $listsIds = array_unique(array_merge($whereIsCreator, $whereIsSubscriber));

        return $listsIds;
    }

    public function getById(int $usersListId, bool $updateCache = false): UsersList
    {
        $cacheKey = KEY_USERS_LIST_SHOW_DATA . $usersListId;
        $usersList = $this->getCachedData($cacheKey, 5 * 60, function () use ($usersListId) {
            return $this->usersList
                ->withCount(['members', 'subscribers'])
                ->where('id', '=', $usersListId)
                ->first();
        }, $updateCache);

        if (empty($usersList)) {
            throw new NotFoundException('List');
        }

        return $usersList;
    }

    public function getByUserId(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_LISTS . $userId;
        $listsIds = $this->getCachedData($cacheKey, null, function () use ($userId) {
            $whereIsCreator = $this->usersList
                ->where('user_id', '=', $userId)
                ->get(['id'])
                ->pluck('id')
                ->toArray();

            $whereIsSubscriber = $this->usersListSubscribtion
                ->where('user_id', '=', $userId)
                ->get(['users_list_id'])
                ->pluck('users_list_id')
                ->toArray();

            return array_unique(array_merge($whereIsCreator, $whereIsSubscriber));
        }, $updateCache);

        return $this->getListsData($listsIds);
    }

    public function create(UsersListDTO $dto, int $userId): void
    {
        $createdUsersList = $this->usersList->create([
            'user_id' => $userId,
            'name' => $dto->name,
            'description' => $dto->description,
            'is_private' => $dto->isPrivate,
            // TODO FILES
            'bg_image' => $dto->bgImage,
        ]);

        if (!empty($createdUsersList)) {
            $this->recacheUserLists($userId);
        }
    }

    public function update(UsersList $usersList, UsersListDTO $dto): void
    {
        $updatingStatus = $usersList->update([
            'name' => $dto->name ?? $usersList->name,
            'description' => $dto->description ?? $usersList->description,
            'is_private' => $dto->isPrivate ?? $usersList->isPrivate,
            // TODO FILES
            'bg_image' => $dto->bgImage ?? $usersList->bgImage,
        ]);


        if (!empty($updatingStatus)) {
            $this->getById($usersList->id, true);

            // TODO QUEUE
            // Recalculate cache
        }
    }

    public function delete(UsersList $usersList): void
    {
        $usersListData = $usersList->toArray();
        $deletingStatus = $usersList->delete();

        if (!empty($deletingStatus)) {
            event(new DeletedUsersListEvent($usersListData));

            // TODO QUEUE
            // Recalculate cache
        }
    }

    public function addMember(int $usersListId, int $userId): void
    {
        if (empty($this->queryUserMembership($usersListId, $userId)->exists())) {
            $this->usersListMember->create([
                'users_list_id' => $usersListId,
                'user_id' => $userId
            ]);
        }
    }

    public function removeMember(int $usersListId, int $userId): void
    {
        if (!empty($usersListMember = $this->queryUserMembership($usersListId, $userId)->first())) {
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

            if (!empty($usersListSubscribtion)) {
                $this->recacheUserLists($userId);
            }
        }
    }

    public function unsubscribe(int $usersListId, int $userId): void
    {
        if (!empty($usersListSubscribtion = $this->queryUserSubscribtion($usersListId, $userId)->first())) {
            $usersListId = $usersListSubscribtion->users_list_id;
            $deletingStatus = $usersListSubscribtion->delete();

            if (!empty($deletingStatus)) {
                $this->recacheUserLists($userId);
            }
        }
    }

    public function recacheUserLists(int $userId): void
    {
        $this->getByUserId($userId, true);
    }

    protected function getListsData(array $usersListsIds): Collection
    {
        return new Collection(array_map(function ($usersListId) {
            return $this->getUsersListData($usersListId);
        }, $usersListsIds));
    }

    protected function getUsersListData(int $usersListId): UsersList
    {
        $cacheKey = KEY_USERS_LIST_DATA . $usersListId;
        return $this->getCachedData($cacheKey, 5 * 60, function () use ($usersListId) {
            return $this->usersList->where('id', $usersListId)->first();
        });
    }
}
