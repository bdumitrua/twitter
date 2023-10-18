<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserSubscribtionRepository
{
    protected $users;
    protected $userSubscribtions;

    public function __construct(
        User $users,
        UserSubscribtion $userSubscribtions
    ) {
        $this->users = $users;
        $this->userSubscribtions = $userSubscribtions;
    }

    protected function baseQuery(): Builder
    {
        return $this->userSubscribtions->newQuery();
    }

    protected function baseQueryWithRelations(array $relations = []): Builder
    {
        return $this->baseQuery()->with($relations);
    }

    protected function queryByBothIds(int $subscriberId, int $userId): Builder
    {
        return $this->baseQuery()
            ->where('subscriber_id', '=', $subscriberId)
            ->where('user_id', '=', $userId);
    }

    protected function subscribtionExist(int $subscriberId, int $userId): bool
    {
        return $this->queryByBothIds($subscriberId, $userId)->exists();
    }

    public function getSubscriptions(int $userId): Collection
    {
        return $this->baseQuery()->where('subscriber_id', '=', $userId)->get();
    }

    public function getSubscribers(int $userId): Collection
    {
        return $this->baseQuery()->where('user_id', '=', $userId)->get();
    }

    public function create(int $subscriberId, int $userId)
    {
        if (empty($this->subscribtionExist($subscriberId, $userId))) {
            $this->userSubscribtions->create([
                'subscriber_id' => $subscriberId,
                'user_id' => $userId
            ]);
        }
    }

    public function remove(int $subscriberId, int $userId): void
    {
        /** @var UserSubscribtion */
        $subscribtion = $this->queryByBothIds($subscriberId, $userId)->first();

        if ($subscribtion) {
            $subscribtion->delete();
        }
    }
}
