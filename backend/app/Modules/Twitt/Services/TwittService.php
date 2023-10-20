<?php

namespace App\Modules\Twitt\Services;

use App\Modules\Twitt\DTO\TwittDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Repositories\TwittRepository;
use App\Modules\Twitt\Requests\TwittRequest;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use Illuminate\Support\Facades\Auth;

class TwittService
{
    private $twittRepository;

    public function __construct(
        TwittRepository $twittRepository
    ) {
        $this->twittRepository = $twittRepository;
    }

    public function show(Twitt $twitt)
    {
        return $this->twittRepository->getById($twitt->id);
    }
    public function user(User $user)
    {
        return $this->twittRepository->getByUserId($user->id);
    }
    public function list(UsersList $usersList)
    {
        return $this->twittRepository->getByUsersListId($usersList->id);
    }
    public function create(TwittRequest $twittRequest)
    {
        return $this->twittRepository->create($twittDTO, Auth::id());
    }
    public function destroy(Twitt $twitt)
    {
        return $this->twittRepository->destroy($twittDTO, Auth::id());
    }

    protected function createDTO(TwittRequest $twittRequest): TwittDTO
    {
        $requestData = $twittRequest->all();

        $filteredData = array_filter($requestData, function ($value) {
            return $value !== null;
        });

        $twittDTO = new TwittDTO();
        foreach ($filteredData as $key => $value) {
            $twittDTO->$key = $value;
        }
    }
}
