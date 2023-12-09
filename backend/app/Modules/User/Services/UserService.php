<?php

namespace App\Modules\User\Services;

use App\Helpers\TimeHelper;
use App\Modules\User\DTO\UserDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Requests\SearchRequest;
use App\Modules\User\Requests\UserUpdateRequest;
use App\Modules\User\Resources\ShortUserResource;
use App\Modules\User\Resources\UserResource;
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserService
{
    use CreateDTO;

    protected UserRepository $userRepository;
    protected LogManager $logger;
    protected ?int $authorizedUserId;

    public function __construct(
        UserRepository $userRepository,
        LogManager $logger,
    ) {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    public function index(): JsonResource
    {
        return new UserResource($this->userRepository->getAuthorizedUser($this->authorizedUserId));
    }

    public function show(User $user): JsonResource
    {
        return new UserResource($this->userRepository->getById($user->id));
    }

    public function update(UserUpdateRequest $userUpdateRequest): void
    {
        $this->logger->info('Creating UserDTO from update request', $userUpdateRequest->toArray());
        $userDTO = $this->createDTO($userUpdateRequest, UserDTO::class);

        $authorizedUser = $this->userRepository->getById($this->authorizedUserId);
        $this->logger->info(
            'Updating User using UserDTO',
            [
                'Current user data' => $authorizedUser->toArray(),
                'DTO' => $userDTO->toArray()
            ]
        );
        $this->userRepository->update(
            $authorizedUser->id,
            $userDTO
        );
    }
}
