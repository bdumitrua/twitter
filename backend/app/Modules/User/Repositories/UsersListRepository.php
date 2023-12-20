<?php

namespace App\Modules\User\Repositories;

use App\Exceptions\NotFoundException;
use App\Helpers\ResponseHelper;
use App\Modules\User\DTO\UsersListDTO;
use App\Modules\User\Events\DeletedUsersListEvent;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListMember;
use App\Modules\User\Models\UsersListSubscribtion;
use App\Traits\GetCachedData;
use App\Traits\UpdateFromDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UsersListRepository
{
    use GetCachedData, UpdateFromDTO;

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

    /**
     * @param int $usersListId
     * @param int $userId
     * 
     * @return Builder
     */
    protected function queryUserMembership(int $usersListId, int $userId): Builder
    {
        return $this->usersListMember->newQuery()
            ->where('users_list_id', '=', $usersListId)
            ->where('user_id', '=', $userId);
    }

    /**
     * @param int $usersListId
     * @param int $userId
     * 
     * @return Builder
     */
    protected function queryUserSubscribtion(int $usersListId, int $userId): Builder
    {
        return $this->usersListSubscribtion->newQuery()
            ->where('users_list_id', '=', $usersListId)
            ->where('user_id', '=', $userId);
    }

    /**
     * @param int $usersListId
     * 
     * @return array
     */
    public function getUsersListMembersIds(int $usersListId): array
    {
        $cacheKey = KEY_USERS_LIST_MEMBERS . $usersListId;
        return $this->getCachedData($cacheKey, null, function () use ($usersListId) {
            return $this->usersListMember->where('users_list_id', $usersListId)
                ->get(['id'])->pluck('id')->toArray();
        });
    }

    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return array
     */
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

    /**
     * @param int $usersListId
     * @param bool $updateCache
     * 
     * @return UsersList
     */
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

        $authorizedUserId = Auth::id();
        if (!empty($authorizedUserId)) {
            $usersList->imSubscribed = !empty($this->queryUserSubscribtion($usersListId, $authorizedUserId));
        }

        return $usersList;
    }

    /**
     * @param int $userId
     * 
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        $listsIds = $this->getUserListsIds($userId);
        return $this->getListsData($listsIds);
    }

    /**
     * @param UsersListDTO $dto
     * @param int $userId
     * 
     * @return void
     */
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

    /**
     * @param UsersList $usersList
     * @param UsersListDTO $dto
     * 
     * @return void
     */
    public function update(UsersList $usersList, UsersListDTO $dto): void
    {
        $updatingStatus = $this->updateFromDto($usersList, $dto);

        if (!empty($updatingStatus)) {
            $this->clearListCache($usersList->id);
        }
    }

    /**
     * @param UsersList $usersList
     * 
     * @return void
     */
    public function delete(UsersList $usersList): void
    {
        $usersListData = $usersList->toArray();
        $deletingStatus = $usersList->delete();

        if (!empty($deletingStatus)) {
            event(new DeletedUsersListEvent($usersListData));

            $this->clearListCache($usersList->id);
        }
    }

    /**
     * @param int $usersListId
     * @param int $userId
     * 
     * @return UsersListMember|null
     */
    public function getMembershipByBothIds(int $usersListId, int $userId): ?UsersListMember
    {
        return $this->queryUserMembership($usersListId, $userId)->first();
    }

    /**
     * @param int $usersListId
     * 
     * @return Collection
     */
    public function members(int $usersListId): Collection
    {
        $members = $this->usersListMember
            ->with('usersData')
            ->where('users_list_id', $usersListId)
            ->get();

        $membersData = [];
        foreach ($members as $member) {
            $member->usersData->users_list_id = $usersListId;
            $membersData[] = $member->usersData;
        }

        return new Collection($membersData);
    }

    /**
     * @param int $usersListId
     * @param int $userId
     * 
     * @return Response
     */
    public function addMember(int $usersListId, int $userId): Response
    {
        $listMemberExists = $this->queryUserMembership($usersListId, $userId)->exists();
        if (!$listMemberExists) {
            $addMember = $this->usersListMember->create([
                'users_list_id' => $usersListId,
                'user_id' => $userId
            ]);

            if (!empty($addMember)) {
                $this->clearListMembersCache($usersListId);
            }
        }

        return ResponseHelper::okResponse(!$listMemberExists);
    }

    /**
     * @param int $usersListId
     * @param int $userId
     * 
     * @return Response
     */
    public function removeMember(int $usersListId, int $userId): Response
    {
        $usersListMember = $this->getMembershipByBothIds($usersListId, $userId);
        $listMemberExists = !empty($usersListMember);

        if ($listMemberExists) {
            $removeMemberStatus = $usersListMember->delete();

            if (!empty($removeMemberStatus)) {
                $this->clearListMembersCache($usersListId);
            }
        }

        return ResponseHelper::okResponse($listMemberExists);
    }

    /**
     * @param int $usersListId
     * @param int $userId
     * 
     * @return UsersListSubscribtion|null
     */
    public function getUserSubscribtionByBothIds(int $usersListId, int $userId): ?UsersListSubscribtion
    {
        return $this->queryUserSubscribtion($usersListId, $userId)->first();
    }

    /**
     * @param int $usersListId
     * 
     * @return Collection
     */
    public function subscribtions(int $usersListId): Collection
    {
        $subscribers = $this->usersListSubscribtion
            ->with('usersData')
            ->where('users_list_id', $usersListId)
            ->get();

        $subscribersData = [];
        foreach ($subscribers as $subscriber) {
            $subscribersData[] = $subscriber->usersData;
        }

        return new Collection($subscribersData);
    }

    /**
     * @param int $usersListId
     * @param int $userId
     * 
     * @return Response
     */
    public function subscribe(int $usersListId, int $userId): Response
    {
        $listSubscribtionExists = $this->queryUserSubscribtion($usersListId, $userId)->exists();
        if (!$listSubscribtionExists) {
            $usersListSubscribtion = $this->usersListSubscribtion->create([
                'users_list_id' => $usersListId,
                'user_id' => $userId
            ]);

            if (!empty($usersListSubscribtion)) {
                $this->clearUserCache($userId);
            }
        }

        return ResponseHelper::okResponse(!$listSubscribtionExists);
    }

    /**
     * @param int $usersListId
     * @param int $userId
     * 
     * @return Response
     */
    public function unsubscribe(int $usersListId, int $userId): Response
    {
        $usersListSubscribtion = $this->getUserSubscribtionByBothIds($usersListId, $userId);
        $listSubscribtionExists = !empty($usersListSubscribtion);

        if ($listSubscribtionExists) {
            $deletingStatus = $usersListSubscribtion->delete();

            if (!empty($deletingStatus)) {
                $this->clearUserCache($userId);
            }
        }

        return ResponseHelper::okResponse($listSubscribtionExists);
    }

    /**
     * @param array $usersListsIds
     * 
     * @return Collection
     */
    protected function getListsData(array $usersListsIds): Collection
    {
        return new Collection(array_map(function ($usersListId) {
            if (!empty($usersList = $this->getUsersListData($usersListId))) {
                return $usersList;
            }
        }, $usersListsIds));
    }

    /**
     * @param int $usersListId
     * 
     * @return UsersList|null
     */
    protected function getUsersListData(int $usersListId): ?UsersList
    {
        $cacheKey = KEY_USERS_LIST_DATA . $usersListId;
        return $this->getCachedData($cacheKey, $this->usersListCacheTime, function () use ($usersListId) {
            return $this->usersList->where('id', $usersListId)->first();
        });
    }

    /**
     * @param int $userId
     * 
     * @return void
     */
    protected function clearUserCache(int $userId): void
    {
        $cacheKey = KEY_USER_LISTS . $userId;
        $this->clearCache($cacheKey);
    }

    /**
     * @param int $usersListId
     * 
     * @return void
     */
    protected function clearListCache(int $usersListId): void
    {
        $cacheKey = KEY_USERS_LIST_SHOW_DATA . $usersListId;
        $this->clearCache($cacheKey);

        $cacheKey = KEY_USERS_LIST_DATA . $usersListId;
        $this->clearCache($cacheKey);
    }

    /**
     * @param int $usersListId
     * 
     * @return void
     */
    protected function clearListMembersCache(int $usersListId): void
    {
        $cacheKey = KEY_USERS_LIST_MEMBERS . $usersListId;
        $this->clearCache($cacheKey);
    }
}
