<?php

namespace App\Modules\Tweet\Repositories;

use App\Helpers\TweetAgeHelper;
use App\Modules\Tweet\DTO\TweetDTO;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Models\TweetFavorite;
use App\Modules\Tweet\Models\TweetLike;
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

class TweetRepository
{
    use GetCachedData;

    protected Tweet $tweet;
    protected TweetLike $tweetLike;
    protected TweetFavorite $tweetFavorite;
    protected UserRepository $userRepository;
    protected UsersListRepository $usersListRepository;
    protected UserGroupRepository $userGroupRepository;
    protected UserSubscribtionRepository $userSubscribtionRepository;

    public function __construct(
        Tweet $tweet,
        TweetLike $tweetLike,
        TweetFavorite $tweetFavorite,
        UserRepository $userRepository,
        UsersListRepository $usersListRepository,
        UserGroupRepository $userGroupRepository,
        UserSubscribtionRepository $userSubscribtionRepository,
    ) {

        $this->tweet = $tweet;
        $this->tweetLike = $tweetLike;
        $this->tweetFavorite = $tweetFavorite;
        $this->userRepository = $userRepository;
        $this->usersListRepository = $usersListRepository;
        $this->userGroupRepository = $userGroupRepository;
        $this->userSubscribtionRepository = $userSubscribtionRepository;
    }

    /**
     * @return Builder
     */
    protected function baseQuery(): Builder
    {
        return $this->tweet->newQuery()
            ->withCount(['likes', 'favorites', 'reposts', 'replies', 'quotes'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * @param mixed $tweetId
     * 
     * @return Builder
     */
    protected function queryById($tweetId): Builder
    {
        return $this->baseQuery()->where('id', '=', $tweetId);
    }

    /**
     * @param mixed $userId
     * 
     * @return Builder
     */
    protected function queryByUserId($userId): Builder
    {
        return $this->baseQuery()->where('user_id', '=', $userId);
    }

    /**
     * @param mixed $userId
     * 
     * @return Builder
     */
    protected function queryLikedByUserId($userId): Builder
    {
        return $this->tweetLike->newQuery()
            ->where('user_id', '=', $userId)
            ->latest();
    }

    /**
     * @param mixed $userId
     * 
     * @return Builder
     */
    protected function queryBookmarksByUserId($userId): Builder
    {
        return $this->tweetFavorite->newQuery()
            ->where('user_id', '=', $userId)
            ->latest();
    }

    /**
     * @param string $text
     * 
     * @return Collection
     */
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

    /**
     * @param int $tweetId
     * 
     * @return Tweet
     */
    public function getById(int $tweetId): Tweet
    {
        $tweet = $this->getTweetData($tweetId);

        return $this->assembleTweetReplies($tweet);
    }

    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return Collection
     */
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

    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return Collection
     */
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

    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return Collection
     */
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
    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return Collection
     */
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

    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return Collection
     */
    public function getUserLikedTweets(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_LIKED_TWEETS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 3 * 60, function () use ($userId) {
            return $this->pluckKey(
                $this->queryLikedByUserId($userId)->get(),
                'tweet_id'
            );
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return Collection
     */
    public function getUserBookmarks(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_BOOKMARKS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 3 * 60, function () use ($userId) {
            return $this->pluckKey(
                $this->queryBookmarksByUserId($userId)->get(),
                'tweet_id'
            );
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    /**
     * @param UsersList $usersList
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return Collection
     */
    public function getFeedByUsersList(UsersList $usersList, int $userId, bool $updateCache = false): Collection
    {
        $usersListId = $usersList->id;
        $cacheKey = KEY_USERS_LIST_FEED . $usersListId;
        $usersListTweets = $this->getCachedData($cacheKey, 15, function () use ($usersListId, $userId) {
            $membersIds = $this->usersListRepository->getUsersListMembersIds($usersListId);
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

    /**
     * @param TweetDTO $tweetDTO
     * 
     * @return void
     */
    public function create(TweetDTO $tweetDTO): void
    {
        $data = $tweetDTO->toArray();
        $data = array_filter($data, fn ($value) => !is_null($value));

        $tweet = $this->tweet->create($data);
        $this->clearUserTweetsCache($tweet->user_id);
    }

    /**
     * @param TweetDTO[] $tweetDTOs 
     * 
     * @return void
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

    /**
     * @param Tweet $tweet
     * 
     * @return void
     */
    public function destroy(Tweet $tweet): void
    {
        $tweetId = $tweet->id;
        $userId = $tweet->user_id;

        $tweet->delete();

        $this->clearTweetCache($tweetId);
        $this->clearUserTweetsCache($userId);
    }

    /**
     * @param int $tweetId
     * @param int $authorizedUserId
     * 
     * @return void
     */
    public function unrepost(int $tweetId, int $authorizedUserId): void
    {
        $tweet = $this->tweet->where('user_id', $authorizedUserId)
            ->where('type', 'repost')
            ->where('linked_tweet_id', $tweetId)
            ->first() ?? [];

        if (!empty($tweet)) {
            $repostTweetId = $tweet->id;
            $tweet->delete();

            $this->clearTweetCache($repostTweetId);
            $this->clearUserTweetsCache($authorizedUserId);
        }
    }

    /**
     * @param array $tweetsIds
     * 
     * @return Collection
     */
    public function getTweetsData(array $tweetsIds): Collection
    {
        return new Collection(array_map(function ($tweetId) {
            return $this->getTweetData($tweetId);
        }, $tweetsIds));
    }

    /**
     * @param int $tweetId
     * 
     * @return Tweet
     */
    public function getTweetData(int $tweetId): Tweet
    {
        $cacheKey = KEY_TWEET_DATA . $tweetId;
        return $this->getCachedData($cacheKey, $this->getTweetCacheTime($tweetId), function () use ($tweetId) {
            return $this->queryById($tweetId)->first();
        });
    }

    /**
     * @param int $tweetId
     * 
     * @return int
     */
    protected function getTweetCacheTime(int $tweetId): int
    {
        return TweetAgeHelper::getTweetAge($this->tweet->find($tweetId));
    }

    /**
     * @param int $tweetId
     * 
     * @return void
     */
    protected function clearTweetCache(int $tweetId): void
    {
        $cacheKey = KEY_TWEET_DATA . $tweetId;
        $this->clearCache($cacheKey);
    }

    /**
     * @param int $userId
     * 
     * @return void
     */
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

    /**
     * @param array $userIds
     * @param array $groupIds
     * 
     * @return Builder
     */
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

    /**
     * @param mixed $relation
     * @param string $key
     * 
     * @return array
     */
    protected function pluckKey($relation, string $key): array
    {
        return $relation->pluck($key)->toArray();
    }

    /**
     * Tweets assembling and work with threads
     */

    /**
     * @param array $tweetsIds
     * 
     * @return Collection
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

    /**
     * @param Tweet $tweet
     * 
     * @return Tweet
     */
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

    /**
     * @param Tweet $tweet
     * 
     * @return void
     */
    private function loadLinkedTweetData(Tweet &$tweet): void
    {
        $needsLinkedTweet = ['reply', 'repost', 'quote'];
        $linkedTweetId = $tweet->linked_tweet_id;

        if (in_array($tweet->type, $needsLinkedTweet) && !empty($linkedTweetId)) {
            $tweet->linkedTweet = $this->getTweetData($linkedTweetId);
        }
    }

    /**
     * @param Tweet $tweet
     * 
     * @return void
     */
    private function loadRepliesData(Tweet &$tweet): void
    {
        $tweetRepliesIds = $this->pluckKey($tweet->replies()->take(15)->get(['id']), 'id');
        $tweet->replies = $this->getTweetsData($tweetRepliesIds);
    }

    /**
     * @param int $tweetId
     * 
     * @return int
     */
    private function findThreadStartId(int $tweetId): int
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

    /**
     * @param int $tweetId
     * @param int|null $startTweetId
     * 
     * @return Tweet
     */
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

    /**
     * @param Collection $tweets
     * @param int|null $startTweetId
     * 
     * @return Tweet
     */
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

    /**
     * @param array $processedTweetIds
     * @param Tweet $thread
     * 
     * @return void
     */
    private function addThreadTweetIdsToProcessed(array &$processedTweetIds, Tweet $thread): void
    {
        $processedTweetIds[] = $thread->id;
        if (!empty($thread->thread)) {
            $this->addThreadTweetIdsToProcessed($processedTweetIds, $thread->thread);
        }
    }
}
