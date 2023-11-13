<?php

namespace App\Modules\Tweet\Repositories;

use App\Helpers\TimeHelper;
use App\Helpers\TweetAgeHelper;
use App\Modules\Tweet\DTO\TweetDTO;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UserRepository;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetRepository
{
    use GetCachedData;

    protected $tweet;
    protected $userRepository;

    public function __construct(
        Tweet $tweet,
        UserRepository $userRepository,
    ) {
        $this->tweet = $tweet;
        $this->userRepository = $userRepository;
    }

    protected function baseQuery(): Builder
    {
        return $this->tweet->newQuery()
            ->with('author')
            ->withCount(['likes', 'favorites', 'reposts', 'replies', 'quotes'])
            ->orderBy('created_at', 'desc');
    }

    protected function queryById($tweetId): Builder
    {
        return $this->baseQuery()->where('id', '=', $tweetId);
    }

    protected function queryByUserId($userId): Builder
    {
        return $this->baseQuery()->where('user_id', '=', $userId);
    }

    public function getById(int $tweetId, ?int $authorizedUserId): Collection
    {
        $cacheKey = KEY_TWEET_DATA . $tweetId;
        $tweet = $this->getCachedData($cacheKey, TweetAgeHelper::getTweetAge(Tweet::findOrFail($tweetId)), function () use ($tweetId) {
            return $this->queryById($tweetId)->first();
        });

        $tweet = $this->filterTweetsByGroup(new Collection($tweet), $authorizedUserId);
        if (empty($tweet)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Tweet not found');
        }

        return $tweet;
    }

    public function getUserFeed(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_AUTH_USER_FEED . $userId;

        return $this->getCachedData($cacheKey, 15, function () use ($userId) {
            $user = $this->getUser($userId);
            $subscribedUserIds = $this->pluckKey($user->subscribtions(), 'user_id');
            $userGroupIds = $this->pluckKey($user->groups_member(), 'id');

            return $this->getFeedQuery($subscribedUserIds, $userGroupIds)->get();
        }, $updateCache);
    }

    public function getByUserId(int $userId, ?int $authorizedUserId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_TWEETS . $userId;
        $userTweets = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryByUserId($userId)->get();
        }, $updateCache);

        return $this->filterTweetsByGroup($userTweets, $authorizedUserId);
    }

    public function getFeedByUsersList(UsersList $usersList, ?int $authorizedUserId, bool $updateCache = false): Collection
    {
        if ($usersList->is_private) {
            if (!in_array($authorizedUserId, $this->pluckKey($usersList->subscribers(), 'user_id'))) {
                throw new HttpException(Response::HTTP_FORBIDDEN, 'You don\'t have acces to this list');
            }
        }

        $cacheKey = KEY_USERS_LIST_FEED . $usersList->id;
        $usersListFeed = $this->getCachedData($cacheKey, 15, function () use ($usersList) {
            $membersIds = $this->pluckKey($usersList->members(), 'id');
            return $this->getFeedQuery($membersIds, null)->get();
        }, $updateCache);

        return $this->filterTweetsByGroup($usersListFeed, $authorizedUserId);
    }

    public function create(TweetDTO $tweetDTO, int $userId): void
    {
        $this->tweet->user_id = $userId;
        foreach ($tweetDTO as $property => $value) {
            if (property_exists($this->tweet, $property)) {
                $this->tweet->{$property} = $value;
            }
        }

        $this->tweet->save();
    }

    public function destroy(Tweet $tweet): void
    {
        $tweet->delete();
    }

    public function recacheUserTweets(int $userId): void
    {
        $this->getByUserId($userId, null, true);
    }

    private function getFeedQuery(array $userIds, ?array $groupIds = null): Builder
    {
        $query = $this->baseQuery()
            ->whereIn('user_id', $userIds)
            ->orderBy('created_at', 'desc')
            ->take(20);

        if ($groupIds !== null) {
            $query->where(function (Builder $query) use ($groupIds) {
                $query->whereNull('user_group_id')
                    ->orWhereIn('user_group_id', $groupIds);
            });
        } else {
            $query->whereNull('user_group_id');
        }

        return $query;
    }

    private function getUser(int $userId): User
    {
        return $this->userRepository->getById($userId);
    }

    private function pluckKey($relation, string $key): array
    {
        return $relation->pluck($key)->toArray();
    }

    protected function getUserGroupIds(int $userId): array
    {
        $user = $this->getUser($userId);
        return $this->pluckKey($user->groups_member, 'id');
    }

    protected function filterTweetsByGroup(Collection $tweets, ?int $authorizedUserId): Collection
    {
        $groupIds = [];
        if ($authorizedUserId) {
            $groupIds = $this->getUserGroupIds($authorizedUserId);
        }

        return $tweets->filter(function ($tweet) use ($groupIds) {
            return is_null($tweet->user_group_id) || in_array($tweet->user_group_id, $groupIds);
        })->values();
    }
}
