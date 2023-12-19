<?php

namespace App\Modules\User\Services;

use App\Modules\User\DTO\UserGroupDTO;
use Illuminate\Http\Request;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Repositories\UserGroupRepository;
use App\Modules\User\Requests\CreateUserGroupRequest;
use App\Modules\User\Requests\UpdateUserGroupRequest;
use App\Modules\User\Resources\UserGroupResource;
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

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

    /**
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return UserGroupResource::collection(
            $this->userGroupRepository->getByUserId(Auth::id())
        );
    }

    /**
     * @param UserGroup $userGroup
     * 
     * @return JsonResource
     */
    public function show(UserGroup $userGroup): JsonResource
    {
        return new UserGroupResource(
            $this->userGroupRepository->getById($userGroup->id)
        );
    }

    /**
     * @param CreateUserGroupRequest $createUserGroupRequest
     * 
     * @return void
     */
    public function create(CreateUserGroupRequest $createUserGroupRequest): void
    {
        $this->logger->info('Creating UserGroupDTO from create request', $createUserGroupRequest->toArray());
        $userGroupDTO = $this->createDTO($createUserGroupRequest, UserGroupDTO::class);

        $this->logger->info('Creating UserGroup using UserGroupDTO', $userGroupDTO->toArray());
        $this->userGroupRepository->create($userGroupDTO, Auth::id());
    }

    /**
     * @param UserGroup $userGroup
     * @param UpdateUserGroupRequest $updateUserGroupRequest
     * 
     * @return void
     */
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

    /**
     * @param UserGroup $userGroup
     * @param Request $request
     * 
     * @return void
     */
    public function destroy(UserGroup $userGroup, Request $request): void
    {
        $this->logger->info('Deleting UserGroup', array_merge($userGroup->toArray(), ['ip' => $request->ip()]));
        $this->userGroupRepository->delete($userGroup);
    }

    /**
     * @param UserGroup $userGroup
     * @param User $user
     * 
     * @return Response
     */
    public function add(UserGroup $userGroup, User $user): Response
    {
        return $this->userGroupRepository->addUser($userGroup->id, $user->id);
    }

    /**
     * @param UserGroup $userGroup
     * @param User $user
     * 
     * @return Response
     */
    public function remove(UserGroup $userGroup, User $user): Response
    {
        return $this->userGroupRepository->removeUser($userGroup->id, $user->id);
    }
}
