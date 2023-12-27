<?php

namespace App\Modules\Notification\Repositories;

use App\Firebase\FirebaseService;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\Notification;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository
{
    use GetCachedData;

    protected Notification $notification;
    protected FirebaseService $firebaseService;

    public function __construct(
        Notification $notification,
        FirebaseService $firebaseService
    ) {
        $this->notification = $notification;
        $this->firebaseService = $firebaseService;
    }

    /**
     * @param int $userId
     * 
     * @return ?array
     */
    public function getByUserId(int $userId): ?array
    {
        $cacheKey = KEY_USER_NOTIFICATIONS . $userId;
        $notifications = $this->getCachedData($cacheKey, 15, function () use ($userId) {
            return $this->firebaseService->getUserNotifications($userId);
        }, false);

        $notificationsFinal = [];
        foreach ($notifications as $uuid => $notification) {
            $notification['uuid'] = $uuid;

            // TODO ADD DATA
            // if (isset(
            //     $notification['linkedEntityId'],
            //     $notification['linkedEntityType']
            // )) {
            //     $notification['linkedEntityData'] = $this->getLinkedEntityData(
            //         $notification['linkedEntityId'],
            //         $notification['linkedEntityType'],
            //     );
            // }

            $notificationsFinal[] = $notification;
        }

        return $notificationsFinal;
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
