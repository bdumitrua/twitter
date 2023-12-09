<?php

namespace App\Modules\Tweet\Repositories;

use App\Helpers\TweetAgeHelper;
use App\Modules\Tweet\DTO\TweetDTO;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Models\TweetLike;
use App\Modules\Tweet\Models\TweetNotice;
use App\Modules\User\Events\NewTweetEvent;
use App\Modules\User\Events\TweetNoticeEvent;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UserGroupRepository;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Repositories\UsersListRepository;
use App\Modules\User\Repositories\UserSubscribtionRepository;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Elastic\ScoutDriverPlus\Support\Query;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetRepository
{
    use GetCachedData;

    protected Tweet $tweet;
    protected TweetLike $tweetLike;
    protected UserRepository $userRepository;
    protected UsersListRepository $usersListRepository;
    protected UserGroupRepository $userGroupRepository;
    protected UserSubscribtionRepository $userSubscribtionRepository;

    public function __construct(
        Tweet $tweet,
        TweetLike $tweetLike,
        UserRepository $userRepository,
        UsersListRepository $usersListRepository,
        UserGroupRepository $userGroupRepository,
        UserSubscribtionRepository $userSubscribtionRepository,
    ) {

        $this->tweet = $tweet;
        $this->tweetLike = $tweetLike;
        $this->userRepository = $userRepository;
        $this->usersListRepository = $usersListRepository;
        $this->userGroupRepository = $userGroupRepository;
        $this->userSubscribtionRepository = $userSubscribtionRepository;
    }

    protected function baseQuery(): Builder
    {
        return $this->tweet->newQuery()
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

    protected function queryLikedByUserId($userId): Builder
    {
        return $this->tweetLike->newQuery()
            ->where('user_id', '=', $userId)
            ->orderBy('created_at', 'desc');
    }

    public function search(string $text): Collection
    {
        $query = Query::match()
            ->field('text')
            ->query($text)
            ->fuzziness('AUTO');

        $searchedTweetsIds = $this->tweet->searchQuery($query)->execute()
            ->models()->pluck('id')->toArray();

        return $this->assembleTweetsCollection($searchedTweetsIds);
    }

    public function getById(int $tweetId): Tweet
    {
        $tweet = $this->getTweetData($tweetId);

        return $this->assembleTweetReplies($tweet);
    }

    public function getUserFeed(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_AUTH_USER_FEED . $userId;
        $userFeedTweetsIds = $this->getCachedData($cacheKey, 5, function () use ($userId) {
            $subscribedUserIds = $this->pluckKey(
                $this->userSubscribtionRepository->getSubscribtions($userId),
                'user_id'
            );
            $userGroupIds = $this->pluckKey(
                $this->userGroupRepository->getByUserId($userId),
                'id'
            );

            return $this->pluckKey(
                $this->getFeedQuery($subscribedUserIds, $userGroupIds)->get(),
                'id'
            );
        }, $updateCache);

        return $this->assembleTweetsCollection($userFeedTweetsIds);
    }

    public function getByUserId(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_TWEETS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->pluckKey(
                $this->queryByUserId($userId)->get(),
                'id'
            );
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    public function getUserReplies(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_REPLIES . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->pluckKey(
                $this->queryByUserId($userId)->where('type', '=', 'reply')->get(),
                'id'
            );
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    // TODO FILES
    // ! DOESN'T WORK
    public function getUserTweetsWithMedia(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_MEDIA_TWEETS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->pluckKey(
                $this->queryByUserId($userId)->whereNotNull('media')->get(),
                'id'
            );
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    public function getUserLikedTweets(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_LIKED_TWEETS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->pluckKey(
                $this->queryLikedByUserId($userId)->get(),
                'tweet_id'
            );
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    public function getFeedByUsersList(UsersList $usersList, int $userId, bool $updateCache = false): Collection
    {
        $usersListId = $usersList->id;
        $cacheKey = KEY_USERS_LIST_FEED . $usersListId;
        $usersListTweets = $this->getCachedData($cacheKey, 15, function () use ($usersListId, $userId) {
            $membersIds = $this->pluckKey(
                $this->usersListRepository->getUsersListMembers($usersListId),
                'id'
            );
            $userGroupIds = $this->pluckKey(
                $this->userGroupRepository->getByUserId($userId),
                'id'
            );

            return $this->pluckKey(
                $this->getFeedQuery($membersIds, $userGroupIds)->get(),
                'id'
            );
        }, $updateCache);

        return $this->assembleTweetsCollection($usersListTweets);
    }

    public function create(TweetDTO $tweetDTO): void
    {
        $data = $tweetDTO->toArray();
        $data = array_filter($data, fn ($value) => !is_null($value));

        $tweet = $this->tweet->create($data);
        $this->clearUserTweetsCache($tweet->user_id);
    }

    /**
     * @param TweetDTO[] $tweetDTOs 
     */
    public function createThread(array $tweetDTOs): void
    {
        $previousTweetId = null;
        $userId = $tweetDTOs[0]->userId;

        foreach ($tweetDTOs as $tweetDTO) {
            $data = $tweetDTO->toArray();
            $data['linked_tweet_id'] = $previousTweetId;
            $data = array_filter($data, fn ($value) => !is_null($value));

            $tweet = $this->tweet->create($data);

            $previousTweetId = $tweet->id;
        }

        $this->clearUserTweetsCache($userId);
    }

    public function destroy(Tweet $tweet): void
    {
        $tweetId = $tweet->id;
        $userId = $tweet->user_id;

        $tweet->delete();

        $this->clearTweetCache($tweetId);
        $this->clearUserTweetsCache($userId);
    }

    protected function getTweetsData(array $tweetsIds): Collection
    {
        return new Collection(array_map(function ($tweetId) {
            return $this->getTweetData($tweetId);
        }, $tweetsIds));
    }

    protected function getTweetData(int $tweetId): Tweet
    {
        $cacheKey = KEY_TWEET_DATA . $tweetId;
        return $this->getCachedData($cacheKey, $this->getTweetCacheTime($tweetId), function () use ($tweetId) {
            return $this->queryById($tweetId)->first();
        });
    }

    protected function getTweetCacheTime(int $tweetId): int
    {
        return TweetAgeHelper::getTweetAge($this->tweet->find($tweetId));
    }

    protected function clearTweetCache(int $tweetId): void
    {
        $cacheKey = KEY_TWEET_DATA . $tweetId;
        $this->clearCache($cacheKey);
    }

    protected function clearUserTweetsCache(int $userId): void
    {
        $userCacheKeys = [
            KEY_USER_MEDIA_TWEETS . $userId,
            KEY_USER_REPLIES . $userId,
            KEY_USER_TWEETS . $userId,
        ];

        foreach ($userCacheKeys as $cacheKey) {
            $this->clearCache($cacheKey);
        }
    }

    protected function getFeedQuery(array $userIds, array $groupIds): Builder
    {
        return $this->baseQuery()
            ->whereIn('user_id', $userIds)
            ->where(function (Builder $query) use ($groupIds) {
                $query->whereNull('user_group_id')
                    ->orWhereIn('user_group_id', $groupIds);
            })
            ->latest()
            ->take(20);
    }

    protected function pluckKey($relation, string $key): array
    {
        return $relation->pluck($key)->toArray();
    }

    /**
     * Tweets assembling and work with threads
     */

    private function assembleTweetsCollection(array $tweetsIds): Collection
    {
        $tweetsData = $this->getTweetsData($tweetsIds);

        $result = new Collection();
        $processedTweetIds = []; // Массив для отслеживания обработанных ID
        foreach ($tweetsIds as $tweetId) {
            if (in_array($tweetId, $processedTweetIds)) {
                continue; // Пропускаем твит, если он уже был обработан
            }

            $tweet = $tweetsData->firstWhere('id', $tweetId);
            if ($tweet->type === 'thread') {
                $threadStartId = $this->findThreadStartId($tweetId);
                $thread = $this->buildThread($threadStartId);

                $result->push($thread);
                $this->addThreadTweetIdsToProcessed($processedTweetIds, $thread);
            } else {
                $this->loadLinkedTweetData($tweet);
                $result->push($tweet);
                $processedTweetIds[] = $tweet->id;
            }
        }

        return $result;
    }

    private function assembleTweetReplies(Tweet $tweet): Tweet
    {
        if ($tweet->type === 'thread' && !empty($tweet->threadChild()->first())) {
            $threadStartId = empty($tweet->linked_tweet_id)
                ? $this->findThreadStartId($tweet->id)
                : $tweet->id;
            $tweet->thread = $this->buildThread($threadStartId, $tweet->id);
        }

        $this->loadLinkedTweetData($tweet);
        $this->loadRepliesData($tweet);

        return $tweet;
    }

    private function loadLinkedTweetData(Tweet &$tweet): void
    {
        $needsLinkedTweet = ['reply', 'repost', 'quote'];
        $linkedTweetId = $tweet->linked_tweet_id;

        if (in_array($tweet->type, $needsLinkedTweet) && !empty($linkedTweetId)) {
            $tweet->linkedTweet = $this->getTweetData($linkedTweetId);
        }
    }

    private function loadRepliesData(Tweet &$tweet): void
    {
        $tweetRepliesIds = $this->pluckKey($tweet->replies()->take(15)->get(['id']), 'id');
        $tweet->replies = $this->getTweetsData($tweetRepliesIds);
    }

    private function findThreadStartId(int $tweetId)
    {
        $cacheKey = KEY_TWEET_THREAD_START_ID . $tweetId;
        return $this->getCachedData($cacheKey, null, function () use ($tweetId) {
            $tweet = $this->getTweetData($tweetId);
            while ($tweet->linked_tweet_id !== null) {
                $tweet = $this->getTweetData($tweet->linked_tweet_id);
            }
            return $tweet->id;
        });
    }

    private function buildThread(int $tweetId, int $startTweetId = null): Tweet
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
        return $this->buildNestedThread($tweets, $startTweetId);
    }

    private function buildNestedThread(Collection $tweets, int $startTweetId = null): Tweet
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

        if (empty($startTweetId)) {
            $thread = $tweets->filter(function ($tweet) {
                return $tweet->linked_tweet_id === null;
            });

            return $thread->first();
        } else {
            return $tweets->firstWhere('id', '>', $startTweetId) ?? new Tweet();
        }
    }

    private function addThreadTweetIdsToProcessed(array &$processedTweetIds, Tweet $thread): void
    {
        $processedTweetIds[] = $thread->id;
        if (!empty($thread->thread)) {
            $this->addThreadTweetIdsToProcessed($processedTweetIds, $thread->thread);
        }
    }
}
