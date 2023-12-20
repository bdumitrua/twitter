<?php

namespace App\Modules\Notification\Repositories;

use App\Helpers\ResponseHelper;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Models\NotificationsSubscribtion;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class NotificationsSubscribtionRepository
{
    use GetCachedData;

    protected NotificationsSubscribtion $notificationsSubscribtion;

    public function __construct(NotificationsSubscribtion $notificationsSubscribtion)
    {
        $this->notificationsSubscribtion = $notificationsSubscribtion;
    }

    /**
     * @param int $userId
     * @param int $subscriberId
     * 
     * @return Builder
     */
    protected function queryByBothIds(int $userId, int $subscriberId): Builder
    {
        return $this->notificationsSubscribtion->newQuery()
            ->where('subscriber_id', '=', $subscriberId)
            ->where('user_id', '=', $userId);
    }

    /**
     * @param int $userId
     * @param int $subscriberId
     * 
     * @return Builder
     */
    public function getByBothIds(int $userId, int $subscriberId): Builder
    {
        return $this->queryByBothIds($userId, $subscriberId)->first();
    }

    /**
     * @param int $userId
     * @param int $subscriberId
     * 
     * @return Response
     */
    public function subscribe(int $userId, int $subscriberId): Response
    {
        $subscribtionExists = $this->queryByBothIds($userId, $subscriberId)->exists();
        if (!$subscribtionExists) {
            $this->notificationsSubscribtion->create([
                'user_id' => $userId,
                'subscriber_id' => $subscriberId,
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
    public function unsubscribe(int $userId, int $subscriberId): Response
    {
        $notificationsSubscribtion = $this->queryByBothIds($userId, $subscriberId)->first();
        $subscribtionExists = !empty($notificationsSubscribtion);

        if ($subscribtionExists) {
            $notificationsSubscribtion->delete();
        }

        return ResponseHelper::okResponse($subscribtionExists);
    }
}
