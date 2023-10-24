<?php

namespace App\Modules\User\Services;

use App\Helpers\TimeHelper;
use App\Modules\User\DTO\UsersListDTO;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UsersListRepository;
use App\Modules\User\Requests\CreateUsersListRequest;
use App\Modules\User\Requests\UpdateUsersListRequest;
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UsersListService
{
    use CreateDTO;

    protected $usersListRepository;

    public function __construct(
        UsersListRepository $usersListRepository,
    ) {
        $this->usersListRepository = $usersListRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return Cache::rememberForever(KEY_USER_LISTS . $authorizedUserId, function () use ($authorizedUserId) {
            return $this->usersListRepository->getByUserId($authorizedUserId);
        });
    }

    public function show(UsersList $usersList): UsersList
    {
        $usersListId = $usersList->id;
        return Cache::remember(KEY_USERS_LIST_DATA . $usersListId, TimeHelper::getMinutes(5), function () use ($usersListId) {
            return $this->usersListRepository->getById(
                $usersListId,
            );
        });
    }

    public function create(CreateUsersListRequest $createUsersListRequest): void
    {
        $authorizedUserId = Auth::id();
        $usersListDTO = $this->createDTO($createUsersListRequest, UsersListDTO::class);

        $createdUsersList = $this->usersListRepository->create($usersListDTO, $authorizedUserId);

        if (!empty($createdUsersList)) {
            $this->recacheUserListsForever($authorizedUserId);
        }
    }

    public function update(UsersList $usersList, UpdateUsersListRequest $updateUsersListRequest): void
    {
        $usersListDTO = $this->createDTO($updateUsersListRequest, UsersListDTO::class);

        $this->usersListRepository->update($usersList, $usersListDTO);

        // TODO QUEUE
        // Сделать добавление в очередь задач на изменение кэша массива списков для каждого подписчика
        // if (!empty($createdUsersList)) {
        //     $this->recacheUserListsForever($usersList->user_id);
        // }
    }

    public function destroy(UsersList $usersList): void
    {
        $this->usersListRepository->delete($usersList);

        // TODO QUEUE
        // Сделать добавление в очередь задач на изменение кэша массива списков для каждого подписчика
        // Минус - перекэш при каждом изменении
        // Плюс - экономия запросов, т.к. изменяются списки (именно данные), не так часто,
        // а запрашиваться могут хоть каждые 5-10 секунд

        // if (!empty($createdUsersList)) {
        //     $this->recacheUserListsForever($usersList->user_id);
        // }
    }

    public function add(UsersList $usersList, User $user): void
    {
        $this->usersListRepository->addMember($usersList->id, $user->id);
    }

    public function remove(UsersList $usersList, User $user): void
    {
        $this->usersListRepository->removeMember($usersList->id, $user->id);
    }

    public function subscribe(UsersList $usersList): void
    {
        $authorizedUserId = Auth::id();
        $subscribtionStatus = $this->usersListRepository->subscribe($usersList->id, $authorizedUserId);

        if (!empty($subscribtionStatus)) {
            $this->recacheUserListsForever($authorizedUserId);
        }
    }

    public function unsubscribe(UsersList $usersList): void
    {
        $authorizedUserId = Auth::id();
        $unsubscribtionStatus = $this->usersListRepository->unsubscribe($usersList->id, $authorizedUserId);

        if (!empty($unsubscribtionStatus)) {
            $this->recacheUserListsForever($authorizedUserId);
        }
    }

    private function recacheUserListsForever(int $userId)
    {
        Cache::forever(KEY_USER_LISTS . $userId, function () use ($userId) {
            return $this->usersListRepository->getByUserId($userId);
        });
    }
}
