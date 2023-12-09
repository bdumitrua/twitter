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
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UsersListService
{
    use CreateDTO;

    protected ?int $authorizedUserId;
    protected UsersListRepository $usersListRepository;
    protected LogManager $logger;

    public function __construct(
        UsersListRepository $usersListRepository,
        LogManager $logger,
    ) {
        $this->usersListRepository = $usersListRepository;
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    public function index(): Collection
    {
        return $this->usersListRepository->getByUserId($this->authorizedUserId);
    }

    public function show(UsersList $usersList): UsersList
    {
        return $this->usersListRepository->getById($usersList->id);
    }

    public function create(CreateUsersListRequest $createUsersListRequest): void
    {
        $this->logger->info('Creating UsersListDTO from create request', $createUsersListRequest->toArray());
        $usersListDTO = $this->createDTO($createUsersListRequest, UsersListDTO::class);

        $this->logger->info('Creating UsersList using UsersListDTO', $usersListDTO->toArray());
        $this->usersListRepository->create($usersListDTO, $this->authorizedUserId);
    }

    public function update(UsersList $usersList, UpdateUsersListRequest $updateUsersListRequest): void
    {
        $this->logger->info('Creating UsersListDTO from update request', $updateUsersListRequest->toArray());
        $usersListDTO = $this->createDTO($updateUsersListRequest, UsersListDTO::class);

        $this->logger->info(
            'Updating UsersList using UsersListDTO',
            [
                'Current usersList' => $usersList->toArray(),
                'DTO' => $usersListDTO->toArray()
            ]
        );
        $this->usersListRepository->update($usersList, $usersListDTO);
    }

    public function destroy(UsersList $usersList, Request $request): void
    {
        $this->logger->info('Deleting UsersList', [$usersList->toArray(), 'ip' => $request->ip()]);
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
        $this->usersListRepository->subscribe($usersList->id, $this->authorizedUserId);
    }

    public function unsubscribe(UsersList $usersList): void
    {
        $this->usersListRepository->unsubscribe($usersList->id, $this->authorizedUserId);
    }
}
