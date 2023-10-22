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
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserService
{
    use CreateDTO, GetCachedData;

    protected $userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function index(): User
    {
        $authorizedUserId = Auth::id();
        return $this->getCachedData('user_base_data:' . $authorizedUserId, function () use ($authorizedUserId) {
            return $this->userRepository->getByIdWithRelations(
                $authorizedUserId,
                ['lists', 'lists_subscribtions']
            );
        }, null);
    }

    public function show(User $user): User
    {
        return $user;
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
