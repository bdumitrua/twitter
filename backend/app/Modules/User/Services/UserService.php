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

    public function index(): JsonResource
    {
        return new UserResource($this->userRepository->getAuthUser(Auth::id()));
    }

    public function show(User $user): JsonResource
    {
        return new UserResource($this->userRepository->getById($user->id));
    }

    public function update(UserUpdateRequest $userUpdateRequest): void
    {
        $userDTO = $this->createDTO($userUpdateRequest, UserDTO::class);

        $this->userRepository->update(
            Auth::id(),
            $userDTO
        );
    }

    public function search(SearchRequest $request): JsonResource
    {
        return ShortUserResource::collection($this->userRepository->search($request->search));
    }
}
