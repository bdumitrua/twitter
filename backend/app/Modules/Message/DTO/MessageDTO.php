<?php

namespace App\Modules\Message\DTO;

class MessageDTO
{
    public ?string $text;
    public ?int $senderId;
    public ?int $linkedEntityId;
    public ?string $linkedEntityType;
    public string $status = 'unread';

    public function __construct(?array $data = null)
    {
        $this->text = $data ? $data['text'] : null;
        $this->senderId = $data ? $data['senderId'] : null;
        $this->linkedEntityId = $data ? $data['linkedEntityId'] : null;
        $this->linkedEntityType = $data ? $data['linkedEntityType'] : null;
    }

    public function toArray()
    {
        return [
            'text' => $this->text,
            'senderId' => $this->senderId,
            'linkedEntityId' => $this->linkedEntityId,
            'linkedEntityType' => $this->linkedEntityType,
            'status' => $this->status,
        ];
    }
}
