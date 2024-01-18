<?php

namespace App\Modules\Notification\Repositories;

use App\Exceptions\NotFoundException;
use App\Firebase\FirebaseService;
use App\Helpers\ResponseHelper;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\UserNotification;
use App\Modules\Tweet\Repositories\TweetRepository;
use App\Modules\User\Repositories\UserRepository;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class NotificationRepository
{
    use GetCachedData;

    protected FirebaseService $firebaseService;
    protected UserNotification $userNotification;
    protected TweetRepository $tweetRepository;
    protected UserRepository $userRepository;

    public function __construct(
        FirebaseService $firebaseService,
        UserNotification $userNotification,
        TweetRepository $tweetRepository,
        UserRepository $userRepository,
    ) {
        $this->firebaseService = $firebaseService;
        $this->userNotification = $userNotification;
        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $userId
     * 
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        $cacheKey = KEY_USER_NOTIFICATIONS . $userId;
        $notifications = $this->getCachedData($cacheKey, 15, function () use ($userId) {
            return $this->firebaseService->getUserNotifications($userId);
        }, false);

        if (empty($notifications)) {
            return [];
        }

        $notificationsFinal = [];
        foreach ($notifications as $uuid => $notification) {
            $notification['uuid'] = $uuid;

            if (isset($notification['relatedTweetId']) || isset($notification['relatedUserId'])) {
                $notification['relatedData'] = $this->getRelatedEntityData(
                    $notification['type'],
                    $notification['relatedTweetId'] ?? null,
                    $notification['relatedUserId'] ?? null,
                );
            }

            $notificationsFinal[] = $notification;
        }

        return $notificationsFinal;
    }

    public function send(NotificationDTO $notificationDTO): void
    {
        $notificationUuid = $this->firebaseService->storeNotification($notificationDTO);
        if (!empty($notificationUuid)) {
            $this->userNotification->create([
                'user_id' => $notificationDTO->userId,
                'notification_uuid' => $notificationUuid
            ]);
        }

        $this->clearUserNotificationsCache($notificationDTO->userId);
    }

    public function read(string $notificationUuid, int $authorizedUserId): Response
    {
        $notificationReaded = $this->firebaseService->readNotification($authorizedUserId, $notificationUuid);
        if (!$notificationReaded) {
            return ResponseHelper::noContent();
        }

        $this->clearUserNotificationsCache($authorizedUserId);
        return ResponseHelper::okResponse();
    }

    public function delete(string $notificationUuid, int $authorizedUserId): Response
    {
        $notificationDeleted = $this->firebaseService->deleteNotification($authorizedUserId, $notificationUuid);
        if (!$notificationDeleted) {
            return ResponseHelper::noContent();
        }

        $this->clearUserNotificationsCache($authorizedUserId);
        return ResponseHelper::okResponse();
    }

    protected function getRelatedEntityData(string $notificationType, ?int $relatedTweetId, ?int $relatedUserId)
    {
        $userNotificationTypes = ['newSubscribtions'];
        $tweetNotificationTypes = ['newNotice'];
        $bothNotificationTypes = ['newLike', 'newTweet'];

        if (in_array($notificationType, $userNotificationTypes)) {
            return $this->userRepository->getUserData($relatedUserId);
        }
        if (in_array($notificationType, $tweetNotificationTypes)) {
            return $this->tweetRepository->getTweetData($relatedTweetId);
        }
        if (in_array($notificationType, $bothNotificationTypes)) {
            $data = [];
            $data['tweet'] = $this->tweetRepository->getTweetData($relatedTweetId);
            $data['user'] = $this->userRepository->getUserData($relatedUserId);

            return $data;
        }

        return null;
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
