<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Events\UserSubscribtionEvent;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserSubscribtionRepository
{
    protected $userSubscribtion;

    public function __construct(
        UserSubscribtion $userSubscribtion
    ) {
        $this->userSubscribtion = $userSubscribtion;
    }

    protected function queryByBothIds(int $userId, int $subscriberId): Builder
    {
        return $this->userSubscribtion
            ->where('subscriber_id', '=', $subscriberId)
            ->where('user_id', '=', $userId);
    }

    public function getSubscribtions(int $userId): Collection
    {
        return $this->userSubscribtion
            ->where('subscriber_id', '=', $userId)
            ->get();
    }

    public function getSubscribers(int $userId): Collection
    {
        return $this->userSubscribtion
            ->where('user_id', '=', $userId)
            ->get();
    }

    public function create(int $userId, int $subscriberId): void
    {
        if (empty($this->queryByBothIds($userId, $subscriberId)->exists())) {
            $this->userSubscribtion->create([
                'subscriber_id' => $subscriberId,
                'user_id' => $userId
            ]);
        }
    }

    public function remove(int $userId, int $subscriberId): void
    {
        if ($subscribtion = $this->queryByBothIds($userId, $subscriberId)->first()) {
            $subscribtion->delete();
        }
    }
}
