<?php

namespace App\Modules\User\Services;

use App\Modules\User\DTO\UserUpdateDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Requests\SearchRequest;
use App\Modules\User\Requests\UserUpdateRequest;
use Elastic\Elasticsearch\Client as ElasticSearch;
use Http\Client\Exception\HttpException as ExceptionHttpException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function index(): User
    {
        return $this->userRepository->getByIdWithRelations(
            Auth::id(),
            ['lists', 'lists_subscribtions']
        );
    }

    public function show(User $user): User
    {
        return $this->userRepository->getByIdWithRelations(
            $user->id,
            ['lists', 'lists_subscribtions']
        );
    }

    public function update(UserUpdateRequest $userUpdateRequest): void
    {
        $requestData = $userUpdateRequest->all();

        $filteredData = array_filter($requestData, function ($value) {
            return $value !== null;
        });

        if (empty($filteredData)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'At least one field must be updated');
        }

        $userUpdateDTO = new UserUpdateDTO();
        foreach ($filteredData as $key => $value) {
            $userUpdateDTO->$key = $value;
        }

        $this->userRepository->update(
            Auth::id(),
            $userUpdateDTO
        );
    }

    public function search(SearchRequest $request): Collection
    {
        return $this->userRepository->search($request->search);
    }
}
