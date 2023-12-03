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
    protected TweetLike $tweetLike;
    protected UserRepository $userRepository;

    public function __construct(
        Tweet $tweet,
        TweetLike $tweetLike,
        UserRepository $userRepository,
    ) {

        $this->tweet = $tweet;
        $this->tweetLike = $tweetLike;
        $this->userRepository = $userRepository;
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

    public function getById(int $tweetId): Tweet
    {
        $tweet = $this->getTweetData($tweetId);

        return $this->assembleTweetReplies($tweet);
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

        return $this->assembleTweetsCollection($userFeedTweetsIds);
    }

    public function getByUserId(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_TWEETS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryByUserId($userId)->get()->pluck('id')->toArray();
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    public function getUserReplies(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_REPLIES . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryByUserId($userId)->where('type', '=', 'reply')->get()->pluck('id')->toArray();
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    // TODO FILES
    // ! DOESN'T WORK
    public function getUserTweetsWithMedia(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_MEDIA_TWEETS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryByUserId($userId)->whereNotNull('media')->get()->pluck('id')->toArray();
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    public function getUserLikedTweets(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_LIKED_TWEETS . $userId;
        $userTweetsIds = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryLikedByUserId($userId)->get()->pluck('tweet_id', 'id')->toArray();
        }, $updateCache);

        return $this->assembleTweetsCollection($userTweetsIds);
    }

    public function getFeedByUsersList(UsersList $usersList, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USERS_LIST_FEED . $usersList->id;
        $usersListTweets = $this->getCachedData($cacheKey, 15, function () use ($usersList) {
            $membersIds = $this->pluckKey($usersList->members(), 'id');
            return $this->getFeedQuery($membersIds, null)->get()->pluck('id')->toArray();
        }, $updateCache);

        return $this->assembleTweetsCollection($usersListTweets);
    }

    public function create(TweetDTO $tweetDTO): void
    {
        $data = $tweetDTO->toArray();
        $data = array_filter($data, fn ($value) => !is_null($value));

        $tweet = $this->tweet->create($data);
        event(new NewTweetEvent($tweet));

        // TODO QUEUE
        $this->checkForNotices($tweet);
    }

    /**
     * @param TweetDTO[] $tweetDTOs 
     */
    public function createThread(array $tweetDTOs): void
    {
        $previousTweetId = null;
        foreach ($tweetDTOs as $tweetDTO) {
            $data = $tweetDTO->toArray();
            $data['linked_tweet_id'] = $previousTweetId;
            $data = array_filter($data, fn ($value) => !is_null($value));

            $tweet = $this->tweet->create($data);
            // TODO QUEUE
            $this->checkForNotices($tweet);
            event(new NewTweetEvent($tweet));

            $previousTweetId = $tweet->id;
        }
    }

    public function destroy(Tweet $tweet): void
    {
        $tweet->delete();
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
        return $this->getCachedData($cacheKey, TweetAgeHelper::getTweetAge(Tweet::findOrFail($tweetId)), function () use ($tweetId) {
            return $this->queryById($tweetId)->first();
        });
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

    private function getUser(int $userId): User
    {
        return $this->userRepository->getById($userId);
    }

    private function pluckKey($relation, string $key): array
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
                $processedTweetIds[] = $tweet->id; // Отметить твит как обработанный
            }
        }

        return $result;
    }

    private function assembleTweetReplies(Tweet $tweet): Tweet
    {
        if ($tweet->type === 'thread') {
            $threadStartId = empty($tweet->linked_tweet_id) ? $this->findThreadStartId($tweet->id) : $tweet->id;
            $tweet->thread = $this->buildThread($threadStartId, $tweet->id);
        }

        $this->loadLinkedTweetData($tweet);
        $this->loadRepliesData($tweet);

        return $tweet;
    }

    private function loadLinkedTweetData(Tweet &$tweet): void
    {
        $needsLinkedTweet = ['reply', 'repost', 'quote'];
        if (in_array($tweet->type, $needsLinkedTweet)) {
            $tweet->load(['linkedTweet' => function ($query) {
                $query->withCount(['likes', 'favorites', 'reposts', 'replies', 'quotes'])
                    ->first();
            }]);
        }
    }

    private function loadRepliesData(Tweet &$tweet): void
    {
        $tweet->load(['replies' => function ($query) {
            $query->withCount(['likes', 'favorites', 'reposts', 'replies', 'quotes'])
                ->take(15);
        }]);
    }

    private function findThreadStartId(int $tweetId)
    {
        $tweet = Tweet::find($tweetId);
        while ($tweet->linked_tweet_id !== null) {
            $tweet = Tweet::find($tweet->linked_tweet_id);
        }
        return $tweet->id;
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

    private function checkForNotices(Tweet $tweet): void
    {
        $tweetText = $tweet->text;
        $tweetId = $tweet->id;
        if (empty($tweetText)) {
            return;
        }

        $words = explode(' ', $tweetText);
        $notices = [];
        foreach ($words as $word) {
            if (strpos($word, '@') === 0) {
                $cleanLink = preg_replace('/[^\w]/', '', substr($word, 1));
                $notices[] = $cleanLink;
            }
        }
        $notices = array_unique($notices);

        $noticedUsers = User::whereIn('link', $notices)->get(['id', 'link'])->toArray();
        $noticesData = array_map(function ($subArray) use ($tweetId) {
            return [
                'link' => $subArray['link'],
                'user_id' => $subArray['id'],
                'tweet_id' => $tweetId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $noticedUsers);

        TweetNotice::insert($noticesData);
        $newTweetNotices = TweetNotice::where('tweet_id', $tweetId)->get();
        foreach ($newTweetNotices as $tweetNotice) {
            event(new TweetNoticeEvent($tweetNotice));
        }
    }
}
