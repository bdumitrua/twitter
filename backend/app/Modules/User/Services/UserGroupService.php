<?php

namespace App\Modules\User\Services;

use App\Modules\User\DTO\UserGroupDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Repositories\UserGroupRepository;
use Illuminate\Support\Facades\Auth;

class UserGroupService
{
    protected $userGroupRepository;

    public function __construct(
        UserGroupRepository $userGroupRepository,
    ) {
        $this->userGroupRepository = $userGroupRepository;
    }

    public function index()
    {
        return $this->userGroupRepository->getById(Auth::id());
    }
    public function create(Request $request)
    {
        $userGroupDTO = $this->createDTO($request);

        return $this->userGroupRepository->create(Auth::id(), $userGroupDTO);
    }
    public function update(UserGroup $userGroup, Request $request)
    {
        $userGroupDTO = $this->createDTO($request);

        return $this->userGroupRepository->update($userGroup, $userGroupDTO);
    }
    public function destroy(UserGroup $userGroup)
    {
        return $this->userGroupRepository->delete($userGroup);
    }
    public function add(UserGroup $userGroup, User $user)
    {
        return $this->userGroupRepository->addUser($userGroup->id, $user->id);
    }
    public function remove(UserGroup $userGroup, User $user)
    {
        return $this->userGroupRepository->removeUser($userGroup->id, $user->id);
    }

    protected function createDTO(Request $request): UserGroupDTO
    {
        return new UserGroupDTO(
            $request->user_id,
            $request->name,
            $request->description,
        );
    }
}
