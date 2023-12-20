<?php

namespace App\Modules\User\Services;

use App\Exceptions\NotFoundException;
use App\Modules\User\DTO\UserDTO;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Requests\UserUpdateRequest;
use App\Modules\User\Resources\UserResource;
use App\Traits\CreateDTO;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

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

    /**
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return new UserResource($this->userRepository->getAuthorizedUser($this->authorizedUserId));
    }

    /**
     * @param User $user
     * 
     * @return JsonResource
     */
    public function show(User $user): JsonResource
    {
        return new UserResource($this->userRepository->getById($user->id));
    }

    /**
     * @param UserUpdateRequest $userUpdateRequest
     * 
     * @return void
     * 
     * @throws NotFoundException
     */
    public function update(UserUpdateRequest $userUpdateRequest): void
    {
        $this->logger->info('Creating UserDTO from update request', $userUpdateRequest->toArray());
        $userDTO = $this->createDTO($userUpdateRequest, UserDTO::class);

        $authorizedUser = $this->userRepository->getUserData($this->authorizedUserId);
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
