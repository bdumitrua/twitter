<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Events\UserSubscribtionEvent;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserSubscribtionRepository
{
    protected $userSubscribtions;

    public function __construct(
        UserSubscribtion $userSubscribtions
    ) {
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

    protected function queryByBothIds(int $userId, int $subscriberId): Builder
    {
        return $this->baseQuery()
            ->where('subscriber_id', '=', $subscriberId)
            ->where('user_id', '=', $userId);
    }

    protected function subscribtionExist(int $userId, int $subscriberId): bool
    {
        return $this->queryByBothIds($userId, $subscriberId)->exists();
    }

    public function getSubscriptions(int $userId): Collection
    {
        return $this->baseQuery()->where('subscriber_id', '=', $userId)->get();
    }

    public function getSubscribers(int $userId): Collection
    {
        return $this->baseQuery()->where('user_id', '=', $userId)->get();
    }

    public function create(int $userId, int $subscriberId): void
    {
        if (empty($this->subscribtionExist($userId, $subscriberId))) {
            $subscribtion = $this->userSubscribtions->create([
                'subscriber_id' => $subscriberId,
                'user_id' => $userId
            ]);

            event(new UserSubscribtionEvent($subscribtion, true));
        }
    }

    public function remove(int $userId, int $subscriberId): void
    {
        /** @var UserSubscribtion */
        $subscribtion = $this->queryByBothIds($userId, $subscriberId)->first();

        if ($subscribtion) {
            event(new UserSubscribtionEvent($subscribtion, false));
            $subscribtion->delete();
        }
    }
}
