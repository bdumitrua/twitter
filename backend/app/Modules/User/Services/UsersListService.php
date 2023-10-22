<?php

namespace App\Modules\User\Services;

use App\Modules\User\DTO\UsersListDTO;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UsersListRepository;
use App\Modules\User\Requests\CreateUsersListRequest;
use App\Modules\User\Requests\UpdateUsersListRequest;
use App\Traits\CreateDTO;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersListService
{
    use CreateDTO, GetCachedData;

    protected $usersListRepository;

    public function __construct(
        UsersListRepository $usersListRepository,
    ) {
        $this->usersListRepository = $usersListRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return $this->getCachedData('user_lists:' . $authorizedUserId, function () use ($authorizedUserId) {
            return $this->usersListRepository->getByUserId($authorizedUserId);
        }, null);
    }

    public function show(UsersList $usersList): UsersList
    {
        $usersListId = $usersList->id;
        return $this->getCachedData('users_list_data:' . $usersListId, function () use ($usersListId) {
            return $this->usersListRepository->getById(
                $usersListId,
                ['members', 'subscribers']
            );
        }, 300);
    }

    public function create(CreateUsersListRequest $createUsersListRequest): void
    {
        $usersListDTO = $this->createDTO($createUsersListRequest, UsersListDTO::class);

        $this->usersListRepository->create($usersListDTO, Auth::id());
    }

    public function update(UsersList $usersList, UpdateUsersListRequest $updateUsersListRequest): void
    {
        $usersListDTO = $this->createDTO($updateUsersListRequest, UsersListDTO::class);

        $this->usersListRepository->update($usersList, $usersListDTO);
    }

    public function destroy(UsersList $usersList): void
    {
        $this->usersListRepository->delete($usersList);
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
        $this->usersListRepository->subscribe($usersList->id, Auth::id());
    }

    public function unsubscribe(UsersList $usersList): void
    {
        $this->usersListRepository->unsubscribe($usersList->id, Auth::id());
    }
}
