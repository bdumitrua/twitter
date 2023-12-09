<?php

namespace App\Modules\User\Services;

use App\Modules\User\DTO\UserGroupDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Repositories\UserGroupRepository;
use App\Modules\User\Requests\CreateUserGroupRequest;
use App\Modules\User\Requests\UpdateUserGroupRequest;
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserGroupService
{
    use CreateDTO;

    protected UserGroupRepository $userGroupRepository;
    protected LogManager $logger;

    public function __construct(
        UserGroupRepository $userGroupRepository,
        LogManager $logger,
    ) {
        $this->userGroupRepository = $userGroupRepository;
        $this->logger = $logger;
    }

    public function index(): Collection
    {
        return $this->userGroupRepository->getByUserId(Auth::id());
    }

    public function show(UserGroup $userGroup): UserGroup
    {
        return $this->userGroupRepository->getById($userGroup->id);
    }

    public function create(CreateUserGroupRequest $createUserGroupRequest): void
    {
        $this->logger->info('Creating UserGroupDTO from create request', $createUserGroupRequest->toArray());
        $userGroupDTO = $this->createDTO($createUserGroupRequest, UserGroupDTO::class);

        $this->logger->info('Creating UserGroup using UserGroupDTO', $userGroupDTO->toArray());
        $this->userGroupRepository->create($userGroupDTO, Auth::id());
    }

    public function update(UserGroup $userGroup, UpdateUserGroupRequest $updateUserGroupRequest): void
    {
        $this->logger->info('Creating UserGroupDTO from update request', $updateUserGroupRequest->toArray());
        $userGroupDTO = $this->createDTO($updateUserGroupRequest, UserGroupDTO::class);

        $this->logger->info(
            'Updating UserGroup using UserGroupDTO',
            [
                'Current userGroup' => $userGroup->toArray(),
                'DTO' => $userGroupDTO->toArray()
            ]
        );
        $this->userGroupRepository->update($userGroup, $userGroupDTO);
    }

    public function destroy(UserGroup $userGroup, Request $request): void
    {
        $this->logger->info('Deleting UserGroup', array_merge($userGroup->toArray(), ['ip' => $request->ip()]));
        $this->userGroupRepository->delete($userGroup);
    }

    public function add(UserGroup $userGroup, User $user): void
    {
        $this->userGroupRepository->addUser($userGroup->id, $user->id);
    }

    public function remove(UserGroup $userGroup, User $user): void
    {
        $this->userGroupRepository->removeUser($userGroup->id, $user->id);
    }
}
