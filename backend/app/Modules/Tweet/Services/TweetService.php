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
use App\Modules\User\Repositories\UserRepository;
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TweetService
{
    use CreateDTO;

    private $tweetRepository;
    private $userRepository;
    private $authorizedUserId;

    public function __construct(
        TweetRepository $tweetRepository,
        UserRepository $userRepository
    ) {
        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;
        $this->authorizedUserId = Auth::id();
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
        $userTweets = $this->tweetRepository->getByUserId($user->id);
        return $this->filterTweetsByGroup($userTweets, $this->authorizedUserId);
    }

    public function list(UsersList $usersList): Collection
    {
        if ($usersList->is_private) {
            if (!in_array($this->authorizedUserId, $this->pluckKey($usersList->subscribers(), 'user_id'))) {
                throw new HttpException(Response::HTTP_FORBIDDEN, 'You don\'t have acces to this list');
            }
        }

        $usersListFeed = $this->tweetRepository->getFeedByUsersList($usersList);
        return $this->filterTweetsByGroup($usersListFeed, $this->authorizedUserId);
    }

    public function show(Tweet $tweet): Collection
    {
        $tweet = $this->tweetRepository->getById($tweet->id);
        $tweetAfterFiltering = $this->filterTweetsByGroup(new Collection($tweet), $this->authorizedUserId);

        if (empty($tweetAfterFiltering)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Tweet not found');
        }

        return $tweetAfterFiltering;
    }

    public function create(TweetRequest $tweetRequest): void
    {
        $this->validateTweetTypeData($tweetRequest);
        $tweetDTO = $this->createDTO($tweetRequest, TweetDTO::class);

        $this->tweetRepository->create($tweetDTO, Auth::id());
    }

    public function destroy(Tweet $tweet): void
    {
        $this->tweetRepository->destroy($tweet);
    }

    private function validateTweetTypeData(TweetRequest $tweetRequest): void
    {
        if (!empty($tweetRequest->type) && empty($tweetRequest->linkedTweetId)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Linked tweet id can\'t be empty, if it\'s not default tweet');
        }
    }

    protected function filterTweetsByGroup(Collection $tweets): Collection
    {
        $groupIds = [];
        if ($this->authorizedUserId) {
            $groupIds = $this->getUserGroupIds($this->authorizedUserId);
        }

        return $tweets->filter(function ($tweet) use ($groupIds) {
            return is_null($tweet->user_group_id) || in_array($tweet->user_group_id, $groupIds);
        })->values();
    }

    protected function getUserGroupIds(int $userId): array
    {
        $user = $this->userRepository->getById($userId);
        return $this->pluckKey($user->groups_member, 'id');
    }

    private function pluckKey($relation, string $key): array
    {
        return $relation->pluck($key)->toArray();
    }
}
