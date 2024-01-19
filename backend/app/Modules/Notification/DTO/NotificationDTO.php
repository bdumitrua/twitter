<?php

namespace App\Modules\Notification\DTO;

class NotificationDTO
{
    public ?int $userId = null;
    public ?string $type = null;
    public ?int $relatedTweetId = null;
    public ?int $relatedUserId = null;
    public ?string $status = 'unread';

    public function toArray()
    {
        return [
            'userId' => $this->userId,
            'relatedTweetId' => $this->relatedTweetId,
            'relatedUserId' => $this->relatedUserId,
            'type' => $this->type,
            'status' => $this->status,
        ];
    }
}
