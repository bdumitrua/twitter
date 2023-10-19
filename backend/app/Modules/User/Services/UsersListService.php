<?php

namespace App\Modules\User\Services;

use App\Modules\User\DTO\UsersListDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
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
        // 
    }

    public function create(UsersListRequest $usersListRequest)
    {
        $usersListDTO = $this->createDTO($usersListRequest);

        //
    }

    public function update(UserGroup $userGroup, UsersListRequest $usersListRequest)
    {
        $usersListDTO = $this->createDTO($usersListRequest);

        // 
    }

    public function destroy(UserGroup $userGroup)
    {
        return $this->usersListRepository->delete($userGroup);
    }

    public function add(UserGroup $userGroup, User $user)
    {
        // 
    }

    public function remove(UserGroup $userGroup, User $user)
    {
        // 
    }

    protected function createDTO(UsersListRequest $usersListRequest): UsersListDTO
    {
        // return new UsersListDTO(

        // );
    }
}
