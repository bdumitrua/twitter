<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\Notification;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository
{
    use GetCachedData;

    protected Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @param int $userId
     * 
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        $cacheKey = KEY_USER_NOTIFICATIONS . $userId;
        return $this->getCachedData($cacheKey, 30, function () use ($userId) {
            return $this->notification->where('user_id', '=', $userId)->take(20)->get();
        }, false);
    }

    /**
     * @param NotificationDTO $notificationDTO
     * 
     * @return Notification
     */
    public function create(NotificationDTO $notificationDTO): Notification
    {
        $data = $notificationDTO->toArray();
        $data = array_filter($data, fn ($value) => !is_null($value));

        $newNotification = $this->notification->create($data);
        $this->clearUserNotificationsCache($newNotification->user_id);

        return $newNotification;
    }

    /**
     * @param Notification $notification
     * @param string $newStatus
     * 
     * @return void
     */
    public function update(Notification $notification, string $newStatus): void
    {
        $notification->update([
            'status' => $newStatus
        ]);

        $this->clearUserNotificationsCache($notification->user_id);
    }

    /**
     * @param int $userId
     * 
     * @return void
     */
    protected function clearUserNotificationsCache(int $userId): void
    {
        $cacheKey = KEY_USER_NOTIFICATIONS . $userId;
        $this->clearCache($cacheKey);
    }
}
