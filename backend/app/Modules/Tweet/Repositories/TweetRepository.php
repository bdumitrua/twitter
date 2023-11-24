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
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetRepository
{
    use GetCachedData;

    protected Tweet $tweet;
    protected UserRepository $userRepository;

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

            return $this->getFeedQuery($subscribedUserIds, $userGroupIds)->get()->pluck('id')->toArray();
        }, $updateCache);

        return $this->getTweetsData($userFeedTweetsIds);
    }

    public function getByUserId(int $userId, bool $updateCache = false)
    {
        $cacheKey = KEY_USER_TWEETS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryByUserId($userId)->get()->pluck('id')->toArray();
        }, true);
        $userTweets = $this->getTweetsData($userTweetsIds);

        $result = new Collection();
        $processedTweetIds = []; // Массив для отслеживания обработанных ID
        foreach ($userTweetsIds as $tweetId) {
            if (in_array($tweetId, $processedTweetIds)) {
                continue; // Пропускаем твит, если он уже был обработан
            }

            $tweet = $userTweets->firstWhere('id', $tweetId);
            if ($tweet->type === 'thread') {
                $threadStartId = $this->findThreadStartId($tweetId);
                $thread = $this->buildThread($threadStartId);

                $this->addThreadTweetIdsToProcessed($processedTweetIds, $thread);
                $result->push($thread);
            } else {
                $result->push($tweet);
                $processedTweetIds[] = $tweet->id; // Отметить твит как обработанный
            }
        }

        return $result;
    }

    private function findThreadStartId($tweetId)
    {
        $tweet = Tweet::find($tweetId);
        while ($tweet->linked_tweet_id !== null) {
            $tweet = Tweet::find($tweet->linked_tweet_id);
        }
        return $tweet->id;
    }

    private function buildThread($tweetId): object
    {
        /* 
            Запрос сначала берёт данные конкретного нашего твита, а затем 
            рекурсивно берёт все твитты, которые ссылаются на данный твит по id
        */
        $sql = "
        WITH RECURSIVE ThreadChain AS (
            SELECT 
                id
            FROM 
                tweets 
            WHERE 
                id = :startTweetId
        
            UNION ALL
        
            SELECT 
                t.id
            FROM 
                tweets t
            INNER JOIN ThreadChain tc ON t.linked_tweet_id = tc.id
            WHERE 
                t.type = 'thread'
        )
        SELECT * FROM ThreadChain;
        ";

        $sqlData = DB::select($sql, ['startTweetId' => $tweetId]);
        $tweetIds = array_map(function ($tweet) {
            return $tweet->id;
        }, $sqlData);
        $tweets = $this->getTweetsData($tweetIds);
        return $this->buildNestedThread($tweets);
    }

    private function buildNestedThread($tweets)
    {
        $tweetsById = [];
        foreach ($tweets as $tweet) {
            $tweetsById[$tweet->id] = $tweet;
            $tweet->thread = [];
        }

        foreach ($tweets as $tweet) {
            if ($tweet->linked_tweet_id !== null && isset($tweetsById[$tweet->linked_tweet_id])) {
                $tweetsById[$tweet->linked_tweet_id]->thread = $tweet;
            }
        }

        // Фильтрация для получения только начальных твитов треда
        $thread = $tweets->filter(function ($tweet) {
            return $tweet->linked_tweet_id === null;
        });

        return $thread->first();
    }

    private function addThreadTweetIdsToProcessed(&$processedTweetIds, $thread)
    {
        $processedTweetIds[] = $thread->id;
        if (!empty($thread->thread)) {
            $this->addThreadTweetIdsToProcessed($processedTweetIds, $thread->thread);
        }
    }

    public function getFeedByUsersList(UsersList $usersList, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USERS_LIST_FEED . $usersList->id;
        $usersListTweets = $this->getCachedData($cacheKey, 15, function () use ($usersList) {
            $membersIds = $this->pluckKey($usersList->members(), 'id');
            return $this->getFeedQuery($membersIds, null)->get()->pluck('id')->toArray();
        }, $updateCache);

        return $this->getTweetsData($usersListTweets);
    }

    public function create(TweetDTO $tweetDTO, int $userId): void
    {
        $data = $tweetDTO->toArray();
        $data['user_id'] = $userId;
        $data = array_filter($data, fn ($value) => !is_null($value));

        $this->tweet->create($data);
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

    private function getTweetsData(array $tweetsIds): Collection
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
