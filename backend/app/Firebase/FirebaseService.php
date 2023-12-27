<?php

namespace App\Firebase;

use App\Modules\Message\DTO\MessageDTO;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\Notification;
use Kreait\Firebase\Database;

class FirebaseService
{
    protected Database $database;
    protected string $bucket;

    public function __construct()
    {
        $this->database = app('firebase.database');
        $this->bucket = config('firebase.projects.app.storage.bucket');
    }

    /**
     * @param Notification $notification
     * 
     * @return void
     */
    public function storeNotification(NotificationDTO $notificationDTO): void
    {
        $this->database->getReference($this->getUserNotificationsPath($notificationDTO->userId))->push(
            array_merge(
                $notificationDTO->toArray(),
                ['created_at' => Database::SERVER_TIMESTAMP]
            )
        );
    }

    public function getUserNotifications(int $userId): ?array
    {
        $notifications = $this->database->getReference($this->getUserNotificationsPath($userId))
            ->orderByKey()
            ->limitToFirst(15)
            ->getSnapshot()
            ->getValue();

        return $notifications;
    }

    /**
     * @param MessageDTO $messageDTO
     * @param int $chatId
     * @param array $participants
     * 
     * @return string|null
     */
    public function sendMessage(MessageDTO $messageDTO, int $chatId, array $participants): ?string
    {
        $newMessageUuid = $this->database->getReference($this->getChatMessagesPath($chatId))->push()->getKey();
        $newMessageData = array_merge(
            $messageDTO->toArray(),
            ['created_at' => Database::SERVER_TIMESTAMP]
        );

        $updates = [];
        foreach ($participants as $userId) {
            $updates[$this->getMessagePath($chatId, $userId, $newMessageUuid)] = $newMessageData;
        }

        $this->database->getReference()->update($updates);

        return $newMessageUuid;
    }

    /**
     * @param string $messageUuid
     * @param int $chatId
     * 
     * @return bool
     */
    public function readMessage(string $messageUuid, int $chatId): bool
    {
        $messageReference = $this->database->getReference($this->getChatMessagesPath($chatId));
        if (!$messageReference->getSnapshot()->exists()) {
            return false;
        }

        $updates = [];
        $participants = array_keys($messageReference->getValue());
        foreach ($participants as $userId) {
            $messagePath = $this->getMessagePath($chatId, $userId, $messageUuid);
            $updates["{$messagePath}/status"] = 'read';
        }

        $this->database->getReference()->update($updates);

        return true;
    }

    /**
     * @param string $messageUuid
     * @param int $chatId
     * @param int $authorizedUserId
     * 
     * @return int|null
     */
    public function deleteMessage(string $messageUuid, int $chatId, int $authorizedUserId): ?int
    {
        $messageReference = $this->database->getReference($this->getMessagePath($chatId, $authorizedUserId, $messageUuid));
        if (!$messageReference->getSnapshot()->exists()) {
            return null;
        }
        $messageReference->remove();

        $newestMessage = $this->database->getReference($this->getUserMessagesPath($chatId, $authorizedUserId))
            ->orderByKey()
            ->limitToFirst(1)
            ->getSnapshot()
            ->getValue();

        if (empty($newestMessage)) {
            return 0;
        }

        // Last chat activity time
        return array_values($newestMessage)[0]['created_at'];
    }

    /**
     * @param int $chatId
     * @param int $authorizedUserId
     * 
     * @return array|null
     */
    public function getChatMessages(int $chatId, int $authorizedUserId): ?array
    {
        $messages = $this->database->getReference($this->getUserMessagesPath($chatId, $authorizedUserId))
            ->orderByKey()
            ->limitToFirst(15)
            ->getSnapshot()
            ->getValue();

        return $messages;
    }

    /**
     * @param int $chatId
     * @param int $authorizedUserId
     * 
     * @return bool
     */
    public function clearChatMessages(int $chatId, int $authorizedUserId): bool
    {
        $chatReference = $this->database->getReference($this->getUserMessagesPath($chatId, $authorizedUserId));
        if (!$chatReference->getSnapshot()->exists()) {
            return false;
        }

        $chatReference->remove();

        return true;
    }

    protected function getUserNotificationsPath(int $userId): string
    {
        return $this->bucket . "/notifications/{$userId}";
    }

    protected function getChatMessagesPath(int $chatId): string
    {
        return $this->bucket . "/messages/{$chatId}";
    }

    protected function getUserMessagesPath(int $chatId, int $userId): string
    {
        return $this->bucket . "/messages/{$chatId}/{$userId}";
    }

    protected function getMessagePath(int $chatId, int $userId, string $messageUuid): string
    {
        return $this->bucket . "/messages/{$chatId}/{$userId}/{$messageUuid}";
    }
}
