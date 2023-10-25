<?php

namespace App\Modules\Tweet\Repositories;

use App\Helpers\TimeHelper;
use App\Modules\Tweet\DTO\TweetDTO;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Events\TweetReplyEvent;
use App\Modules\User\Events\TweetRepostEvent;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetRepository
{
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
            ->withCount(['likes', 'favorites', 'comments', 'reposts', 'replies']);
    }

    public function getById(int $tweetId, ?int $userId): Tweet
    {
        $cacheKey = KEY_TWEET_DATA . $tweetId;
        $userGroupIds = [];

        if (!empty($userId)) {
            $user = $this->getUserWithRelations($userId, ['groups_member']);
            $userGroupIds = $this->pluckIds($user->groups_member);
        }

        // TODO HOT
        // Динамический расчёт времени кэша
        $tweet = Cache::remember($cacheKey, TimeHelper::getSeconds(15), function () use ($tweetId, $userGroupIds) {
            return $this->baseQuery()
                ->where('id', '=', $tweetId)
                ->first();
        });

        if (
            $tweet->user_group_id !== null
            && !in_array($tweet->user_group_id, $userGroupIds)
        ) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Tweet not found');
        }

        return $tweet;
    }

    public function getByUserId(int $userId, ?int $authorizedUserId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_TWEETS . $userId;
        $userGroupIds = [];

        if ($updateCache) {
            $userTweets = $this->baseQuery()
                ->where('user_id', '=', $userId)->get();

            Cache::put($cacheKey, $userTweets, TimeHelper::getMinutes(5));
        }

        if (!empty($authorizedUserId)) {
            $user = $this->getUserWithRelations($userId, ['groups_member']);
            $userGroupIds = $this->pluckIds($user->groups_member);
        }

        $userTweets = Cache::remember($cacheKey, TimeHelper::getMinutes(5), function () use ($userId) {
            return $this->baseQuery()
                ->where('user_id', '=', $userId)->get();
        });

        $filteredTweets = $userTweets->filter(function ($tweet) use ($userGroupIds) {
            return is_null($tweet->user_group_id) || in_array($tweet->user_group_id, $userGroupIds);
        })->values();

        return $filteredTweets;
    }
    public function getUserFeed(int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_AUTH_USER_FEED . $userId;

        if ($updateCache) {
            $user = $this->getUserWithRelations($userId, ['subscribtions_data', 'groups_member']);
            $subscribedUserIds = $this->pluckIds($user->subscribtions);
            $userGroupIds = $this->pluckIds($user->groups_member);

            $userFeed = $this->getFeedQuery($subscribedUserIds, $userGroupIds)->get();
            Cache::put($cacheKey, $userFeed, TimeHelper::getSeconds(15));
        }

        return Cache::remember($cacheKey, TimeHelper::getSeconds(15), function () use ($userId) {
            $user = $this->getUserWithRelations($userId, ['subscribtions_data', 'groups_member']);
            $subscribedUserIds = $this->pluckIds($user->subscribtions);
            $userGroupIds = $this->pluckIds($user->groups_member);

            return $this->getFeedQuery($subscribedUserIds, $userGroupIds)->get();
        });
    }

    public function getFeedByUsersList(UsersList $usersList, ?int $userId, bool $updateCache = false): Collection
    {
        $cacheKey = KEY_USERS_LIST_FEED . $usersList->id;
        $userGroupIds = [];

        if ($updateCache) {
            if (!empty($userId)) {
                $user = $this->getUserWithRelations($userId, ['groups_member']);
                $userGroupIds = $this->pluckIds($user->groups_member);
            }

            $membersIds = $this->pluckIds($usersList->members());
            $usersListFeed = $this->getFeedQuery($membersIds, $userGroupIds)->get();

            Cache::put($cacheKey, $usersListFeed, TimeHelper::getSeconds(15));
        }

        return Cache::remember($cacheKey, TimeHelper::getSeconds(15), function () use ($usersList, $userId, $userGroupIds) {
            if (!empty($userId)) {
                $user = $this->getUserWithRelations($userId, ['groups_member']);
                $userGroupIds = $this->pluckIds($user->groups_member);
            }

            $membersIds = $this->pluckIds($usersList->members());
            return $this->getFeedQuery($membersIds, $userGroupIds)->get();
        });
    }

    public function create(TweetDTO $tweetDTO, int $userId): void
    {
        $filledGroups = $this->validateFilledGroups($tweetDTO);
        $this->fillTweetFields($tweetDTO, $userId, $filledGroups);
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

    private function getUserWithRelations(int $userId): User
    {
        return $this->userRepository->getById($userId);
    }

    private function pluckIds($relation): array
    {
        return $relation->pluck('id')->toArray();
    }

    protected function validateFilledGroups(TweetDTO $tweetDTO): array
    {
        $filledGroups = [];

        $groups = [
            'comment' => [$tweetDTO->isComment, $tweetDTO->commentedTweetId],
            'reply' => [$tweetDTO->isReply, $tweetDTO->repliedTweetId],
            'repost' => [$tweetDTO->isRepost, $tweetDTO->repostedTweetId],
        ];

        foreach ($groups as $key => [$isFlag, $id]) {
            if ($isFlag !== null && $id !== null) {
                $filledGroups[] = $key;
            }
        }

        if (count($filledGroups) > 1) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                'Только одна из групп полей (Комментарий, Цитата, Репост) может быть заполнена'
            );
        }

        return $filledGroups;
    }

    protected function fillTweetFields(TweetDTO $tweetDTO, int $userId, array $filledGroups): void
    {
        $this->tweet->text = $tweetDTO->text;
        $this->tweet->user_id = $userId;

        if (!empty($tweetDTO->userGroupId)) {
            $this->tweet->user_group_id = $tweetDTO->userGroupId;
        }

        $groupMultipleName = [
            'reply' => 'repli',
        ];

        foreach ($filledGroups as $group) {
            $this->tweet->{"is_{$group}"} = true;

            $group = $groupMultipleName[$group] ?? $group;
            $this->tweet->{"{$group}ed_tweet_id"} = $tweetDTO->{"{$group}edTweetId"};
        }
    }
}
