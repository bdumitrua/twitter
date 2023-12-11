<?php

namespace App\Modules\User\Repositories;

use App\Exceptions\NotFoundException;
use App\Modules\User\DTO\UsersListDTO;
use App\Modules\User\Events\DeletedUsersListEvent;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListMember;
use App\Modules\User\Models\UsersListSubscribtion;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UsersListRepository
{
    use GetCachedData;

    protected int $usersListCacheTime = 5 * 60;

    protected UsersList $usersList;
    protected UsersListMember $usersListMember;
    protected UsersListSubscribtion $usersListSubscribtion;

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

    public function getUsersListMembersIds(int $usersListId): array
    {
        $cacheKey = KEY_USERS_LIST_MEMBERS . $usersListId;
        return $this->getCachedData($cacheKey, null, function () use ($usersListId) {
            return $this->usersListMember->where('users_list_id', $usersListId)
                ->get(['id'])->pluck('id')->toArray();
        });
    }

    public function getUserListsIds(int $userId, bool $updateCache = false): array
    {
        $cacheKey = KEY_USER_LISTS . $userId;
        return $this->getCachedData($cacheKey, null, function () use ($userId) {
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
        }, $updateCache);
    }

    public function getById(int $usersListId, bool $updateCache = false): UsersList
    {
        $cacheKey = KEY_USERS_LIST_SHOW_DATA . $usersListId;
        $usersList = $this->getCachedData($cacheKey, $this->usersListCacheTime, function () use ($usersListId) {
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

    public function getByUserId(int $userId): Collection
    {
        $listsIds = $this->getUserListsIds($userId);
        return $this->getListsData($listsIds);
    }

    public function create(UsersListDTO $dto, int $userId): void
    {
        $data = $dto->toArray();
        $data = array_filter($data, fn ($value) => !is_null($value));
        $data = array_merge($data, ['user_id' => $userId]);

        // TODO FILES
        $createdUsersList = $this->usersList->create($data);

        if (!empty($createdUsersList)) {
            $this->clearUserCache($userId);
        }
    }

    public function update(UsersList $usersList, UsersListDTO $dto): void
    {
        // TODO вынести в трейт
        $dtoProperties = get_object_vars($dto);
        foreach ($dtoProperties as $property => $value) {
            if (!empty($value)) {
                $usersList->$property = $value;
            }
        }

        $updatingStatus = $usersList->save();

        if (!empty($updatingStatus)) {
            $this->clearListCache($usersList->id);
        }
    }

    public function delete(UsersList $usersList): void
    {
        $usersListData = $usersList->toArray();
        $deletingStatus = $usersList->delete();

        if (!empty($deletingStatus)) {
            event(new DeletedUsersListEvent($usersListData));

            $this->clearListCache($usersList->id);
        }
    }

    public function subscribtions(int $usersListId): Collection
    {
        $subscribers = $this->usersListSubscribtion->with('usersData')
            ->where('users_list_id', $usersListId)->get();

        $subscribersData = [];
        foreach ($subscribers as $subscriber) {
            $subscribersData[] = $subscriber->usersData;
        }

        return new Collection($subscribersData);
    }

    public function members(int $usersListId): Collection
    {
        $members = $this->usersListMember->with('usersData')
            ->where('users_list_id', $usersListId)->get();

        $membersData = [];
        foreach ($members as $member) {
            $member->usersData->users_list_id = $usersListId;
            $membersData[] = $member->usersData;
        }

        return new Collection($membersData);
    }

    public function addMember(int $usersListId, int $userId): void
    {
        if (empty($this->queryUserMembership($usersListId, $userId)->exists())) {
            $addMember = $this->usersListMember->create([
                'users_list_id' => $usersListId,
                'user_id' => $userId
            ]);

            if (!empty($addMember)) {
                $this->clearListMembersCache($usersListId);
            }
        }
    }

    public function removeMember(int $usersListId, int $userId): void
    {
        if (!empty($usersListMember = $this->queryUserMembership($usersListId, $userId)->first())) {
            $removeMemberStatus = $usersListMember->delete();

            if (!empty($removeMemberStatus)) {
                $this->clearListMembersCache($usersListId);
            }
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
                $this->clearUserCache($userId);
            }
        }
    }

    public function unsubscribe(int $usersListId, int $userId): void
    {
        if (!empty($usersListSubscribtion = $this->queryUserSubscribtion($usersListId, $userId)->first())) {
            $usersListId = $usersListSubscribtion->users_list_id;
            $deletingStatus = $usersListSubscribtion->delete();

            if (!empty($deletingStatus)) {
                $this->clearUserCache($userId);
            }
        }
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
        return $this->getCachedData($cacheKey, $this->usersListCacheTime, function () use ($usersListId) {
            return $this->usersList->where('id', $usersListId)->first();
        });
    }

    protected function clearUserCache(int $userId): void
    {
        $cacheKey = KEY_USER_LISTS . $userId;
        $this->clearCache($cacheKey);
    }

    protected function clearListCache(int $usersListId): void
    {
        $cacheKey = KEY_USERS_LIST_SHOW_DATA . $usersListId;
        $this->clearCache($cacheKey);

        $cacheKey = KEY_USERS_LIST_DATA . $usersListId;
        $this->clearCache($cacheKey);
    }

    protected function clearListMembersCache(int $usersListId): void
    {
        $cacheKey = KEY_USERS_LIST_MEMBERS . $usersListId;
        $this->clearCache($cacheKey);
    }
}
