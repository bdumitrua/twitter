<?php

namespace App\Modules\User\Repositories;

use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
use App\Helpers\TimeHelper;
use App\Modules\User\DTO\UsersListDTO;
use App\Modules\User\Events\DeletedUsersListEvent;
use App\Modules\User\Events\UsersListMembersUpdateEvent;
use App\Modules\User\Events\UsersListSubscribtionEvent;
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

    public function getById(int $usersListId, ?int $authorizedUserId, bool $updateCache = false): UsersList
    {
        $cacheKey = KEY_USERS_LIST_DATA . $usersListId;

        $usersList = $this->getCachedData($cacheKey, 5 * 60, function () use ($usersListId) {
            return $this->usersList
                ->withCount(['members', 'subscribers'])
                ->with(['subscribers'])
                ->where('id', '=', $usersListId)
                ->first();
        }, $updateCache);

        if (empty($usersList)) {
            throw new NotFoundException('List');
        }

        if ($usersList->is_private) {
            if (!in_array($authorizedUserId, $usersList->subscribers->pluck('user_id')->toArray())) {
                throw new AccessDeniedException();
            }
        }

        return $usersList;
    }

    public function getByUserId(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_LISTS . $userId;

        return $this->getCachedData($cacheKey, null, function () use ($userId) {
            return $this->queryByUserId($userId)->get();
        }, $updateCache);
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
            $this->getById($usersList->id, null, true);

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
            $usersListMember = $this->usersListMember->create([
                'users_list_id' => $usersListId,
                'user_id' => $userId
            ]);
        }
    }

    public function removeMember(int $usersListId, int $userId): void
    {
        if (!empty($usersListMember = $this->queryUserMembership($usersListId, $userId)->first())) {
            $deletingStatus = $usersListMember->delete();
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
}
