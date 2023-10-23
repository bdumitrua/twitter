<?php

namespace App\Modules\User\Services;

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
        return Cache::remember('auth_user_data:' . $authorizedUserId, now()->addMinutes(1), function () use ($authorizedUserId) {
            return $this->userRepository->getByIdWithRelations(
                $authorizedUserId,
                ['lists', 'lists_subscribtions']
            );
        });
    }

    public function show(User $user): User
    {
        $userId = $user->id;
        return Cache::remember('user_base_data:' . $userId, now()->addMinutes(1), function () use ($userId) {
            return $this->userRepository->getByIdWithRelations(
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
