<?php

namespace App\Modules\Message\DTO;

class MessageDTO
{
    public ?string $text;
    public ?int $senderId;
    public ?int $receiverId;
    public ?int $linkedEntityId;
    public ?string $linkedEntityType;
    public string $status = 'unread';

    public function __construct(array $data)
    {
        $this->text = $data['text'];
        $this->senderId = $data['senderId'];
        $this->receiverId = $data['receiverId'];
        $this->linkedEntityId = $data['linkedEntityId'];
        $this->linkedEntityType = $data['linkedEntityType'];
    }
}
