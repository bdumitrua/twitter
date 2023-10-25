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

    /**
     * Для пользователя - кэш на 5 минут, при создании - рекэш на 30 мин
     * 
     * Для списков - кэш на 15 сек
     * 
     * Для ленты - кэш на 15 сек
     */

    public function index(): Collection
    {
        return $this->tweetRepository->getUserFeed(Auth::id());
    }

    public function user(User $user): Collection
    {
        return $this->tweetRepository->getByUserId($user->id, Auth::id());
    }

    public function list(UsersList $usersList): Collection
    {
        return $this->tweetRepository->getFeedByUsersList($usersList, Auth::id());
    }

    public function show(Tweet $tweet): Tweet
    {
        return $this->tweetRepository->getById($tweet->id, Auth::id());
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
