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

    public function sendMessage(MessageDTO $messageDTO): ?string
    {
        return $this->database->getReference('messages')->push($messageDTO->toArray())->getKey();
    }

    public function getMessage(string $messageUuid)
    {
        $messageReference = $this->database->getReference("messages/{$messageUuid}");
        if (!$messageReference->getSnapshot()->exists()) {
            return [];
        }

        return $messageReference->getValue();
    }

    public function readMessage(string $messageUuid): bool
    {
        $messageReference = $this->database->getReference("messages/{$messageUuid}");
        if (!$messageReference->getSnapshot()->exists()) {
            return false;
        }

        $updates = ["messages/{$messageUuid}/status" => "read"];
        $this->database->getReference()->update($updates);

        return true;
    }

    public function deleteMessage(string $messageUuid): bool
    {
        $messageReference = $this->database->getReference("messages/{$messageUuid}");
        if (!$messageReference->getSnapshot()->exists()) {
            return false;
        }

        $messageReference->remove();
        return true;
    }
}
