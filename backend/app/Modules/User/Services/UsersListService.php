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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersListService
{
    protected $usersListRepository;

    public function __construct(
        UsersListRepository $usersListRepository,
    ) {
        $this->usersListRepository = $usersListRepository;
    }

    public function index(): Collection
    {
        return $this->usersListRepository->getByUserId(Auth::id());
    }

    public function show(UsersList $usersList): UsersList
    {
        return $this->usersListRepository->getById(
            $usersList->id,
            ['members', 'subscribers']
        );
    }

    public function create(CreateUsersListRequest $createUsersListRequest): void
    {
        $usersListDTO = $this->createDTO($createUsersListRequest);

        $this->usersListRepository->create($usersListDTO, Auth::id());
    }

    public function update(UsersList $usersList, UpdateUsersListRequest $updateUsersListRequest): void
    {
        $usersListDTO = $this->createDTO($updateUsersListRequest);

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

    protected function createDTO(Request $request): UsersListDTO
    {
        $filteredRequestData = array_filter($request->all());

        if (empty($filteredRequestData)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'At least one field must be filled');
        }

        $usersListDTO = new UsersListDTO();
        foreach ($filteredRequestData as $key => $value) {
            $usersListDTO->$key = $value;
        }

        return $usersListDTO;
    }
}
