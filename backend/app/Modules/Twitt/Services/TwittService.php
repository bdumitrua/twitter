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
use App\Traits\CreateDTO;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TwittService
{
    use CreateDTO, GetCachedData;

    private $twittRepository;

    public function __construct(
        TwittRepository $twittRepository
    ) {
        $this->twittRepository = $twittRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return $this->getCachedData('user_feed:' . $authorizedUserId, function () use ($authorizedUserId) {
            return $this->twittRepository->getUserFeed($authorizedUserId);
        }, 1);
    }

    public function user(User $user): Collection
    {
        return $this->getCachedData('user_twitts:' . $user->id, function () use ($user) {
            return $this->twittRepository->getByUserId($user->id);
        }, 5);
    }

    public function list(UsersList $usersList): Collection
    {
        return $this->getCachedData('users_list_feed:' . $usersList->id, function () use ($usersList) {
            return $this->twittRepository->getFeedByUsersList($usersList, Auth::id());
        }, 1);
    }

    public function show(Twitt $twitt): Twitt
    {
        return $this->twittRepository->getById($twitt->id);
    }

    public function create(TwittRequest $twittRequest): void
    {
        $twittDTO = $this->createDTO($twittRequest, TwittDTO::class);

        $this->twittRepository->create($twittDTO, Auth::id());
    }

    public function destroy(Twitt $twitt): void
    {
        $this->twittRepository->destroy($twitt);
    }
}
