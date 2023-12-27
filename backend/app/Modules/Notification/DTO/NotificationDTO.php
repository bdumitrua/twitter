<?php

namespace App\Modules\Notification\DTO;

class NotificationDTO
{
    public ?int $userId = null;
    public ?string $type = null;
    public ?int $relatedId = null;
    public ?string $status = 'unread';

    public function toArray()
    {
        return [
            'userId' => $this->userId,
            'relatedId' => $this->relatedId,
            'type' => $this->type,
            'status' => $this->status,
        ];
    }
}
