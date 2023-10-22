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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TwittService
{
    private $twittRepository;

    public function __construct(
        TwittRepository $twittRepository
    ) {
        $this->twittRepository = $twittRepository;
    }

    public function index(): Collection
    {
        return $this->twittRepository->getFeedByUserId(Auth::id());
    }

    public function show(Twitt $twitt): Twitt
    {
        return $this->twittRepository->getById($twitt->id);
    }

    public function user(User $user): Collection
    {
        return $this->twittRepository->getByUserId($user->id);
    }

    public function list(UsersList $usersList): Collection
    {
        return $this->twittRepository->getFeedByUsersListId($usersList->id);
    }

    public function create(TwittRequest $twittRequest): void
    {
        $twittDTO = $this->createDTO($twittRequest);

        $this->twittRepository->create($twittDTO, Auth::id());
    }

    public function destroy(Twitt $twitt): void
    {
        $this->twittRepository->destroy($twitt);
    }

    protected function createDTO(TwittRequest $twittRequest): TwittDTO
    {
        $requestData = $twittRequest->all();

        $twittDTO = new TwittDTO();
        foreach ($requestData as $key => $value) {
            $twittDTO->$key = $value;
        }

        return $twittDTO;
    }
}
