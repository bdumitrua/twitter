<?php

namespace App\Modules\Search\DTO;

class RecentSearchDTO
{
    public ?string $text = null;
    public ?int $userId = null;
    public ?int $linkedUserId = null;

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'user_id' => $this->userId,
            'linked_user_id' => $this->linkedUserId,
        ];
    }
}
