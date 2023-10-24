<?php

namespace App\Modules\Tweet\Repositories;

use App\Modules\Tweet\DTO\TweetDTO;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Events\TweetReplyEvent;
use App\Modules\User\Events\TweetRepostEvent;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    public function getById(int $id, array $relations = []): Tweet
    {
        return $this->tweet->with($relations)
            ->with('author')
            ->withCount(['likes', 'favorites', 'comments', 'reposts', 'replies'])
            ->where('id', '=', $id)->first() ?? new Tweet();
    }

    public function getByUserId(int $userId, array $relations = []): Collection
    {
        return $this->tweet->with($relations)
            ->with('author')
            ->withCount(['likes', 'favorites', 'comments', 'reposts', 'replies'])
            ->where('user_id', '=', $userId)->get() ?? new Tweet();
    }

    public function getUserFeed(int $userId): Collection
    {
        $user = $this->getUserWithRelations($userId, ['subscribtions_data', 'groups_member']);
        $subscribedUserIds = $this->pluckIds($user->subscribtions);
        $userGroupIds = $this->pluckIds($user->groups_member);

        return $this->getFeedQuery($subscribedUserIds, $userGroupIds)->get();
    }

    public function getFeedByUsersList(UsersList $usersList, ?int $userId): Collection
    {
        $userGroupIds = [];
        if (!empty($userId)) {
            $user = $this->getUserWithRelations($userId, ['groups_member']);
            $userGroupIds = $this->pluckIds($user->groups_member);
        }

        $membersIds = $this->pluckIds($usersList->members());

        return $this->getFeedQuery($membersIds, $userGroupIds)->get();
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

    private function getFeedQuery(array $userIds, ?array $groupIds = null): Builder
    {
        $query = $this->tweet
            ->whereIn('user_id', $userIds)
            ->orderBy('created_at', 'desc')
            ->withCount(['likes', 'favorites', 'comments', 'reposts', 'replies'])
            ->with('author')
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

    private function getUserWithRelations(int $userId, array $relations = []): User
    {
        return $this->userRepository->getById($userId, $relations);
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
