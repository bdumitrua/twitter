<?php

namespace App\Modules\Twitt\Services;

use App\Helpers\TimeHelper;
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TwittService
{
    use CreateDTO;

    private $twittRepository;

    public function __construct(
        TwittRepository $twittRepository
    ) {
        $this->twittRepository = $twittRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return Cache::remember(KEY_AUTH_USER_FEED . $authorizedUserId, TimeHelper::getMinutes(1), function () use ($authorizedUserId) {
            return $this->twittRepository->getUserFeed($authorizedUserId);
        });
    }

    public function user(User $user): Collection
    {
        return Cache::remember(KEY_USER_TWITTS . $user->id, TimeHelper::getMinutes(5), function () use ($user) {
            return $this->twittRepository->getByUserId($user->id);
        });
    }

    public function list(UsersList $usersList): Collection
    {
        return Cache::remember(KEY_USERS_LIST_FEED . $usersList->id, TimeHelper::getMinutes(1), function () use ($usersList) {
            return $this->twittRepository->getFeedByUsersList($usersList, Auth::id());
        });
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
