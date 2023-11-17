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

    public function getById(int $tweetId): Tweet
    {
        $cacheKey = KEY_TWEET_DATA . $tweetId;
        return $this->getCachedData($cacheKey, TweetAgeHelper::getTweetAge(Tweet::findOrFail($tweetId)), function () use ($tweetId) {
            return $this->queryById($tweetId)->first();
        });
    }

    public function getUserFeed(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_AUTH_USER_FEED . $userId;
        $userFeedTweetsIds = $this->getCachedData($cacheKey, 15, function () use ($userId) {
            $user = $this->getUser($userId);
            $subscribedUserIds = $this->pluckKey($user->subscribtions(), 'user_id');
            $userGroupIds = $this->pluckKey($user->groups_member(), 'id');

            return $this->getFeedQuery($subscribedUserIds, $userGroupIds)->get('id');
        }, $updateCache);

        return $this->getTweetsData($userFeedTweetsIds);
    }

    public function getByUserId(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_TWEETS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryByUserId($userId)->get('id');
        }, $updateCache);

        return $this->getTweetsData($userTweetsIds);
    }

    public function getFeedByUsersList(UsersList $usersList, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USERS_LIST_FEED . $usersList->id;
        $usersListTweets = $this->getCachedData($cacheKey, 15, function () use ($usersList) {
            $membersIds = $this->pluckKey($usersList->members(), 'id');
            return $this->getFeedQuery($membersIds, null)->get();
        }, $updateCache);

        return $this->getTweetsData($usersListTweets);
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
        $this->getByUserId($userId, true);
    }

    private function getFeedQuery(array $userIds, ?array $groupIds = null): Builder
    {
        return $this->baseQuery()
            ->whereIn('user_id', $userIds)
            ->where(function (Builder $query) use ($groupIds) {
                if ($groupIds) {
                    $query->where(function (Builder $query) use ($groupIds) {
                        $query->whereNull('user_group_id')
                            ->orWhereIn('user_group_id', $groupIds);
                    });
                } else {
                    $query->whereNull('user_group_id');
                }
            })
            ->orderBy('created_at', 'desc')
            ->take(20);
    }

    private function getTweetsData(array $tweetsIds)
    {
        return new Collection(array_map(function ($tweetId) {
            return $this->getById($tweetId);
        }, $tweetsIds));
    }

    private function getUser(int $userId): User
    {
        return $this->userRepository->getById($userId);
    }

    private function pluckKey($relation, string $key): array
    {
        return $relation->pluck($key)->toArray();
    }
}
