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
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserService
{
    use CreateDTO;

    protected $userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function index(): User
    {
        $authorizedUserId = Auth::id();
        return Cache::remember(KEY_AUTH_USER_DATA . $authorizedUserId, TimeHelper::getMinutes(1), function () use ($authorizedUserId) {
            return $this->userRepository->getById(
                $authorizedUserId
            );
        });
    }

    public function show(User $user): User
    {
        $userId = $user->id;
        return Cache::remember(KEY_USER_DATA . $userId, TimeHelper::getMinutes(1), function () use ($userId) {
            return $this->userRepository->getById(
                $userId,
            );
        });
    }

    public function update(UserUpdateRequest $userUpdateRequest): void
    {
        $userDTO = $this->createDTO($userUpdateRequest, UserDTO::class);

        $this->userRepository->update(
            Auth::id(),
            $userDTO
        );
    }

    public function search(SearchRequest $request): Collection
    {
        return $this->userRepository->search($request->search);
    }
}
