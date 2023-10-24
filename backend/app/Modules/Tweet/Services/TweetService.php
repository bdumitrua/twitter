<?php

namespace App\Modules\Tweet\Services;

use App\Helpers\TimeHelper;
use App\Modules\Tweet\DTO\TweetDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Repositories\TweetRepository;
use App\Modules\Tweet\Requests\TweetRequest;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TweetService
{
    use CreateDTO;

    private $tweetRepository;

    public function __construct(
        TweetRepository $tweetRepository
    ) {
        $this->tweetRepository = $tweetRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return Cache::remember(KEY_AUTH_USER_FEED . $authorizedUserId, TimeHelper::getMinutes(1), function () use ($authorizedUserId) {
            return $this->tweetRepository->getUserFeed($authorizedUserId);
        });
    }

    public function user(User $user): Collection
    {
        return Cache::remember(KEY_USER_tweetS . $user->id, TimeHelper::getMinutes(5), function () use ($user) {
            return $this->tweetRepository->getByUserId($user->id);
        });
    }

    public function list(UsersList $usersList): Collection
    {
        return Cache::remember(KEY_USERS_LIST_FEED . $usersList->id, TimeHelper::getMinutes(1), function () use ($usersList) {
            return $this->tweetRepository->getFeedByUsersList($usersList, Auth::id());
        });
    }

    public function show(Tweet $tweet): Tweet
    {
        return $this->tweetRepository->getById($tweet->id);
    }

    public function create(TweetRequest $tweetRequest): void
    {
        $tweetDTO = $this->createDTO($tweetRequest, TweetDTO::class);

        $this->tweetRepository->create($tweetDTO, Auth::id());
    }

    public function destroy(Tweet $tweet): void
    {
        $this->tweetRepository->destroy($tweet);
    }
}
