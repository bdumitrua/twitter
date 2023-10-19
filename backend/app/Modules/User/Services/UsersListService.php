<?php

namespace App\Modules\User\Services;

use App\Modules\User\DTO\UsersListDTO;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UsersListRepository;
use App\Modules\User\Requests\UsersListRequest;
use Illuminate\Support\Facades\Auth;

class UsersListService
{
    protected $usersListRepository;

    public function __construct(
        UsersListRepository $usersListRepository,
    ) {
        $this->usersListRepository = $usersListRepository;
    }

    public function index()
    {
        return $this->usersListRepository->getByUserId(Auth::id());
    }

    public function show(UsersList $usersList)
    {
        return $this->usersListRepository->getById(
            $usersList->id,
            ['members', 'subscribers']
        );
    }

    public function create(UsersListRequest $usersListRequest)
    {
        $usersListDTO = $this->createDTO($usersListRequest);

        return $this->usersListRepository->create($usersListDTO, Auth::id());
    }

    public function update(UsersList $usersList, UsersListRequest $usersListRequest)
    {
        $usersListDTO = $this->createDTO($usersListRequest);

        return $this->usersListRepository->update($usersList, $usersListDTO);
    }

    public function destroy(UsersList $usersList)
    {
        return $this->usersListRepository->delete($usersList);
    }

    public function add(UsersList $usersList, User $user)
    {
        return $this->usersListRepository->addMember($usersList->id, $user->id);
    }

    public function remove(UsersList $usersList, User $user)
    {
        return $this->usersListRepository->removeMember($usersList->id, $user->id);
    }

    public function subscribe(UsersList $usersList)
    {
        return $this->usersListRepository->subscribe($usersList->id, Auth::id());
    }

    public function unsubscribe(UsersList $usersList)
    {
        return $this->usersListRepository->unsubscribe($usersList->id, Auth::id());
    }

    protected function createDTO(UsersListRequest $usersListRequest): UsersListDTO
    {
        return new UsersListDTO(
            $usersListRequest->name,
            $usersListRequest->description ?? '',
            $usersListRequest->bg_image ?? '',
            $usersListRequest->is_private ?? false,
        );
    }
}
