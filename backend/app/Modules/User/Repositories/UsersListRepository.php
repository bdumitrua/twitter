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
use Illuminate\Support\Facades\Cache;

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
        return $this->usersListSubscribtion->newQuery()
            ->where('users_list_id', '=', $usersListId)
            ->where('user_id', '=', $userId);
    }

    protected function queryByUserId(int $userId, array $relations = [])
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

        return $this->usersList->newQuery()
            ->whereIn('id', $listsIds)
            ->with($relations);
    }

    public function getById(int $id, array $relations = []): UsersList
    {
        return $this->usersList->with($relations)
            ->withCount(['members', 'subscribers'])
            ->where('id', '=', $id)
            ->first() ?? new UsersList();
    }

    public function getByUserId(int $userId, array $relations = [], bool $updateCache = false)
    {
        $cacheKey = KEY_USER_LISTS . $userId . KEY_WITH_RELATIONS . implode(',', $relations);

        if ($updateCache) {
            $userGroups = $this->queryByUserId($userId, $relations)->get();
            Cache::forever($cacheKey, $userGroups);
        }

        return Cache::rememberForever($cacheKey, function () use ($userId, $relations) {
            return $this->queryByUserId($userId, $relations)->get();
        });
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
        $usersList->update([
            'name' => $dto->name ?? $usersList->name,
            'description' => $dto->description ?? $usersList->description,
            'is_private' => $dto->isPrivate ?? $usersList->isPrivate,
            // TODO FILES
            'bg_image' => $dto->bgImage ?? $usersList->bgImage,
        ]);

        // if (!empty($updatingStatus)) {
        // TODO QUEUE
        // Сделать добавление в очередь задач на изменение кэша массива списков для каждого подписчика
        // Минус - перекэш при каждом изменении
        // Плюс - экономия запросов, т.к. изменяются списки (именно данные), не так часто,
        // а запрашиваться могут хоть каждые 5-10 секунд
        // }
    }

    public function delete(UsersList $usersList): void
    {
        $usersList->delete();

        // if (!empty($deletingStatus)) {
        // TODO QUEUE
        // Сделать добавление в очередь задач на изменение кэша массива списков для каждого подписчика
        // Минус - перекэш при каждом изменении
        // Плюс - экономия запросов, т.к. изменяются списки (именно данные), не так часто,
        // а запрашиваться могут хоть каждые 5-10 секунд
        // }
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

            if (!empty($usersListSubscribtion)) {
                event(new UsersListSubscribtionEvent($usersListId, true));
                $this->recacheUserLists($userId);
            }
        }
    }

    public function unsubscribe(int $usersListId, int $userId)
    {
        if (!empty($usersListSubscribtion = $this->queryUserSubscribtion($usersListId, $userId)->first())) {
            $usersListId = $usersListSubscribtion->users_list_id;
            $deletingStatus = $usersListSubscribtion->delete();

            if (!empty($deletingStatus)) {
                event(new UsersListSubscribtionEvent($usersListId, false));
                $this->recacheUserLists($userId);
            }
        }
    }

    private function recacheUserLists(int $userId)
    {
        $this->getByUserId($userId, [], true);
    }
}
