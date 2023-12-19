<?php

namespace App\Modules\User\Repositories;

use App\Helpers\ResponseHelper;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class UserSubscribtionRepository
{
    protected $userSubscribtion;

    public function __construct(
        UserSubscribtion $userSubscribtion
    ) {
        $this->userSubscribtion = $userSubscribtion;
    }

    /**
     * @param int $userId
     * @param int $subscriberId
     * 
     * @return Builder
     */
    protected function queryByBothIds(int $userId, int $subscriberId): Builder
    {
        return $this->userSubscribtion
            ->where('subscriber_id', '=', $subscriberId)
            ->where('user_id', '=', $userId);
    }

    /**
     * @param int $userId
     * 
     * @return Collection
     */
    public function getSubscribtions(int $userId): Collection
    {
        return $this->userSubscribtion
            ->where('subscriber_id', '=', $userId)
            ->get();
    }

    /**
     * @param int $userId
     * 
     * @return Collection
     */
    public function getSubscribers(int $userId): Collection
    {
        return $this->userSubscribtion
            ->where('user_id', '=', $userId)
            ->get();
    }

    /**
     * @param int $userId
     * @param int $subscriberId
     * 
     * @return UserSubscribtion|null
     */
    public function getByBothIds(int $userId, int $subscriberId): ?UserSubscribtion
    {
        return $this->queryByBothIds($userId, $subscriberId)->first();
    }

    /**
     * @param int $userId
     * @param int $subscriberId
     * 
     * @return Response
     */
    public function create(int $userId, int $subscriberId): Response
    {
        $subscribtionExists = $this->queryByBothIds($userId, $subscriberId)->exists();
        if (!$subscribtionExists) {
            $this->userSubscribtion->create([
                'subscriber_id' => $subscriberId,
                'user_id' => $userId,
            ]);
        }

        return ResponseHelper::okResponse(!$subscribtionExists);
    }

    /**
     * @param int $userId
     * @param int $subscriberId
     * 
     * @return Response
     */
    public function remove(int $userId, int $subscriberId): Response
    {
        $userSubscribtion = $this->getByBothIds($userId, $subscriberId);
        $subscribtionExists = !empty($userSubscribtion);

        if ($subscribtionExists) {
            $userSubscribtion->delete();
        }

        return ResponseHelper::okResponse($subscribtionExists);
    }
}
