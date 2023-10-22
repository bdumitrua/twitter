<?php

namespace App\Modules\Twitt\Repositories;

use App\Modules\Twitt\DTO\TwittDTO;
use App\Modules\Twitt\Models\Twitt;
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

    protected function baseQuery(): Builder
    {
        return $this->twitt->newQuery();
    }

    protected function baseQueryWithRelations(array $relations = []): Builder
    {
        return $this->baseQuery()->with($relations);
    }

    protected function queryById(int $id, array $relations = []): Builder
    {
        return $this->baseQueryWithRelations($relations)->where('id', '=', $id);
    }

    protected function queryByUserId(int $id, array $relations = []): Builder
    {
        return $this->baseQueryWithRelations($relations)->where('user_id', '=', $id);
    }

    protected function queryFeedByUsersIds(array $usersIds = [])
    {
        // 
    }

    public function getById(int $id, array $relations = []): Twitt
    {
        return $this->queryById($id, $relations)->first() ?? new Twitt();
    }

    public function getByUserId(int $userId, array $relations = []): Collection
    {
        return $this->queryByUserId($userId, $relations)->get() ?? new Collection();
    }

    public function getUserFeed(int $userId)
    {
        $user = $this->userRepository->getByIdWithRelations(
            $userId,
            ['subscribtions', 'groups_member']
        );

        $subscribedUserIds = $user->subscribtions->pluck('id')->toArray();
        $userGroupIds = $user->groups_member->pluck('id')->toArray();

        return $this->baseQuery()
            ->whereIn('user_id', $subscribedUserIds)
            ->where(function (Builder $query) use ($userGroupIds) {
                // Твиты без группы
                $query->whereNull('user_group_id')
                    // или твиты из групп, в которых состоит пользователь
                    ->orWhereIn('user_group_id', $userGroupIds);
            })
            ->orderBy('created_at', 'desc')
            ->with('author')
            ->take(20)
            ->get();
    }

    public function getFeedByUsersList(UsersList $usersList, int $userId)
    {
        $user = $this->userRepository->getByIdWithRelations(
            $userId,
            ['groups_member']
        );

        $userGroupIds = $user->groups_member->pluck('id')->toArray();
        $membersIds = $usersList->members()->pluck('id')->toArray();

        return $this->baseQuery()
            ->whereIn('user_id', $membersIds)
            ->where(function (Builder $query) use ($userGroupIds) {
                // Твиты без группы
                $query->whereNull('user_group_id')
                    // или твиты из групп, в которых состоит пользователь
                    ->orWhereIn('user_group_id', $userGroupIds);
            })
            ->orderBy('created_at', 'desc')
            ->with('author')
            ->take(20)
            ->get();
    }

    public function create(TwittDTO $twittDTO, int $userId)
    {
        $filledGroups = $this->validateFilledGroups($twittDTO);
        $this->fillTwittFields($twittDTO, $userId, $filledGroups);
        $this->twitt->save();
    }

    protected function validateFilledGroups(TwittDTO $twittDTO)
    {
        $filledGroups = [];

        $groups = [
            'comment' => [$twittDTO->isComment, $twittDTO->commentedTwittId],
            'quote' => [$twittDTO->isQuoute, $twittDTO->quotedTwittId],
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

        foreach ($filledGroups as $group) {
            $this->twitt->{"is_{$group}"} = true;
            $this->twitt->{"{$group}ed_twitt_id"} = $twittDTO->{"{$group}edTwittId"};
        }
    }

    public function destroy(Twitt $twitt)
    {
        $twitt->delete();
    }
}
