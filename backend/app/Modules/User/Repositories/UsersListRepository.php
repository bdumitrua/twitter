<?php

namespace App\Modules\User\Repositories;

use App\Helpers\TimeHelper;
use App\Modules\User\DTO\UsersListDTO;
use App\Modules\User\Events\UsersListMembersUpdateEvent;
use App\Modules\User\Events\UsersListSubscribtionEvent;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListMember;
use App\Modules\User\Models\UsersListSubscribtion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

    protected function queryByUserId(int $userId): Builder
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
            ->whereIn('id', $listsIds);
    }

    public function getById(int $usersListId, ?int $authorizedUserId): UsersList
    {
        $cacheKey = KEY_USERS_LIST_DATA . $usersListId;

        $usersList = Cache::remember($cacheKey, TimeHelper::getMinutes(5), function () use ($usersListId) {
            return $this->usersList
                ->withCount(['members', 'subscribers'])
                ->with(['subscribers_data'])
                ->where('id', '=', $usersListId)
                ->first() ?? new UsersList();
        });

        if ($usersList->is_private) {
            if (!in_array($authorizedUserId, $usersList->subscribers()->pluck('user_id')->toArray())) {
                throw new HttpException(Response::HTTP_FORBIDDEN, 'You don\'t have acces to this list');
            }
        }

        return $usersList;
    }

    public function getByUserId(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_LISTS . $userId;

        if ($updateCache) {
            $userGroups = $this->queryByUserId($userId)->get();
            Cache::forever($cacheKey, $userGroups);
        }

        return Cache::rememberForever($cacheKey, function () use ($userId) {
            return $this->queryByUserId($userId)->get();
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
        $updatingStatus = $usersList->update([
            'name' => $dto->name ?? $usersList->name,
            'description' => $dto->description ?? $usersList->description,
            'is_private' => $dto->isPrivate ?? $usersList->isPrivate,
            // TODO FILES
            'bg_image' => $dto->bgImage ?? $usersList->bgImage,
        ]);


        // TODO QUEUE
        if (!empty($updatingStatus)) {
            $listSubscribers = $usersList->subscribers_data()->pluck('user_id')->toArray();
            // dispatch(new RecalculateUsersLists($listSubscribers));
        }
    }

    public function delete(UsersList $usersList): void
    {
        $listSubscribers = $usersList->subscribers_data()->pluck('user_id')->toArray();
        $deletingStatus = $usersList->delete();

        // TODO QUEUE
        if (!empty($deletingStatus)) {
            // dispatch(new RecalculateUsersLists($listSubscribers));
        }
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

    public function unsubscribe(int $usersListId, int $userId): void
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

    public function recacheUserLists(int $userId): void
    {
        $this->getByUserId($userId, true);
    }
}
