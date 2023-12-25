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

    public function storeNotification(Notification $notification)
    {
        $this->database->getReference('notifications')->push($notification->toArray());
    }

    public function getMessage(string $messageUuid, int $chatId)
    {
        $messageReference = $this->database->getReference("messages/{$chatId}/{$messageUuid}");
        if (!$messageReference->getSnapshot()->exists()) {
            return [];
        }

        return $messageReference->getValue();
    }

    public function sendMessage(MessageDTO $messageDTO, int $chatId): ?string
    {
        return $this->database->getReference("messages/{$chatId}/")->push(array_merge(
            $messageDTO->toArray(),
            ['created_at' => Database::SERVER_TIMESTAMP]
        ))->getKey();
    }

    public function readMessage(string $messageUuid, int $chatId): bool
    {
        $messageReference = $this->database->getReference("messages/{$chatId}/{$messageUuid}");
        if (!$messageReference->getSnapshot()->exists()) {
            return false;
        }

        $updates = ["messages/{$chatId}/{$messageUuid}/status" => "read"];
        $this->database->getReference()->update($updates);

        return true;
    }

    public function deleteMessage(string $messageUuid, int $chatId): ?int
    {
        $messageReference = $this->database->getReference("messages/{$chatId}/{$messageUuid}");
        if (!$messageReference->getSnapshot()->exists()) {
            return null;
        }
        $messageReference->remove();

        $newestMessage = $this->database->getReference("messages/{$chatId}")
            ->orderByKey()
            ->limitToFirst(1)
            ->getSnapshot()
            ->getValue();

        $chatLastActivityTime = $newestMessage['created_at'];
        return $chatLastActivityTime;
    }

    public function getChatMessages(int $chatId): array
    {
        $messages = $this->database->getReference("messages/{$chatId}")
            ->orderByKey()
            ->limitToFirst(15)
            ->getSnapshot()
            ->getValue();

        return $messages;
    }
}
