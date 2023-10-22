<?php

namespace App\Modules\Twitt\Repositories;

use App\Modules\Twitt\DTO\TwittDTO;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\User\Events\TwittReplyEvent;
use App\Modules\User\Events\TwittRepostEvent;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TwittRepository
{
    protected $twitt;
    protected $userRepository;

    public function __construct(
        Twitt $twitt,
        UserRepository $userRepository,
    ) {
        $this->twitt = $twitt;
        $this->userRepository = $userRepository;
    }

    public function getById(int $id, array $relations = []): Twitt
    {
        return $this->twitt->with($relations)
            ->where('id', '=', $id)->first() ?? new Twitt();
    }

    public function getByUserId(int $userId, array $relations = []): Collection
    {
        return $this->twitt->with($relations)
            ->where('user_id', '=', $userId)->get() ?? new Twitt();
    }

    public function getUserFeed(int $userId)
    {
        $user = $this->getUserWithRelations($userId, ['subscribtions', 'groups_member']);
        $subscribedUserIds = $this->pluckIds($user->subscribtions);
        $userGroupIds = $this->pluckIds($user->groups_member);

        return $this->getFeedQuery($subscribedUserIds, $userGroupIds)->get();
    }

    public function getFeedByUsersList(UsersList $usersList, ?int $userId)
    {
        $userGroupIds = [];
        if (!empty($userId)) {
            $user = $this->getUserWithRelations($userId, ['groups_member']);
            $userGroupIds = $this->pluckIds($user->groups_member);
        }

        $membersIds = $this->pluckIds($usersList->members());

        return $this->getFeedQuery($membersIds, $userGroupIds)->get();
    }

    public function create(TwittDTO $twittDTO, int $userId)
    {
        $filledGroups = $this->validateFilledGroups($twittDTO);
        $this->fillTwittFields($twittDTO, $userId, $filledGroups);
        $this->twitt->save();

        if (in_array('reply', $filledGroups)) {
            event(new TwittReplyEvent($this->twitt->id, true));
        } elseif (in_array('repost', $filledGroups)) {
            event(new TwittRepostEvent($this->twitt->id, true));
        }
    }

    public function destroy(Twitt $twitt)
    {
        if ($twitt->is_reply) {
            event(new TwittReplyEvent($twitt->id, false));
        } elseif ($twitt->is_repost) {
            event(new TwittRepostEvent($twitt->id, false));
        }

        $twitt->delete();
    }

    private function getFeedQuery(array $userIds, ?array $groupIds = null)
    {
        $query = $this->twitt
            ->whereIn('user_id', $userIds)
            ->orderBy('created_at', 'desc')
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

    private function getUserWithRelations(int $userId, array $relations = [])
    {
        return $this->userRepository->getByIdWithRelations($userId, $relations);
    }

    private function pluckIds($relation)
    {
        return $relation->pluck('id')->toArray();
    }

    protected function validateFilledGroups(TwittDTO $twittDTO)
    {
        $filledGroups = [];

        $groups = [
            'comment' => [$twittDTO->isComment, $twittDTO->commentedTwittId],
            'reply' => [$twittDTO->isReply, $twittDTO->repliedTwittId],
            'repost' => [$twittDTO->isRepost, $twittDTO->repostedTwittId],
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

    protected function fillTwittFields(TwittDTO $twittDTO, int $userId, array $filledGroups): void
    {
        $this->twitt->text = $twittDTO->text;
        $this->twitt->user_id = $userId;

        if (!empty($twittDTO->userGroupId)) {
            $this->twitt->user_group_id = $twittDTO->userGroupId;
        }

        $groupMultipleName = [
            'reply' => 'repli',
        ];

        foreach ($filledGroups as $group) {
            $this->twitt->{"is_{$group}"} = true;

            $group = $groupMultipleName[$group] ?? $group;
            $this->twitt->{"{$group}ed_twitt_id"} = $twittDTO->{"{$group}edTwittId"};
        }
    }
}
