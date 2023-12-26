<?php

namespace App\Firebase;

use App\Modules\Message\DTO\MessageDTO;
use App\Modules\Notification\Models\Notification;
use Kreait\Firebase\Database;

class FirebaseService
{
    protected Database $database;

    public function __construct()
    {
        $this->database = app('firebase.database');
    }

    /**
     * @param Notification $notification
     * 
     * @return void
     */
    public function storeNotification(Notification $notification): void
    {
        $this->database->getReference('notifications')->push($notification->toArray());
    }

    /**
     * @param string $messageUuid
     * @param int $chatId
     * 
     * @return array|null
     */
    public function getMessage(string $messageUuid, int $chatId): ?array
    {
        $messageReference = $this->database->getReference("messages/{$chatId}/{$messageUuid}");
        if (!$messageReference->getSnapshot()->exists()) {
            return [];
        }

        return $messageReference->getValue();
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
        $pathToChatMessages = "messages/{$chatId}";

        $newMessageUuid = $this->database->getReference($pathToChatMessages)->push()->getKey();
        $newMessageData = array_merge(
            $messageDTO->toArray(),
            ['created_at' => Database::SERVER_TIMESTAMP]
        );

        $updates = [];
        foreach ($participants as $userId) {
            $updates["{$pathToChatMessages}/{$userId}/{$newMessageUuid}"] = $newMessageData;
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
        $pathToChatMessages = "messages/{$chatId}";
        $messageReference = $this->database->getReference($pathToChatMessages);
        if (!$messageReference->getSnapshot()->exists()) {
            return false;
        }

        $updates = [];
        $participants = array_keys($messageReference->getValue());
        foreach ($participants as $userId) {
            $updates["{$pathToChatMessages}/{$userId}/{$messageUuid}/status"] = 'read';
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
        $messageReference = $this->database->getReference("messages/{$chatId}/{$authorizedUserId}/{$messageUuid}");
        if (!$messageReference->getSnapshot()->exists()) {
            return null;
        }
        $messageReference->remove();

        $newestMessage = $this->database->getReference("messages/{$chatId}/{$authorizedUserId}")
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
        $messages = $this->database->getReference("messages/{$chatId}/{$authorizedUserId}")
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
        $pathToChatMessages = "messages/{$chatId}";
        $chatReference = $this->database->getReference("{$pathToChatMessages}/{$authorizedUserId}");
        if (!$chatReference->getSnapshot()->exists()) {
            return false;
        }

        $chatReference->remove();

        return true;
    }
}
