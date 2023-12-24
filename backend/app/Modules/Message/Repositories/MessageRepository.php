<?php

namespace App\Modules\Message\Repositories;

use App\Modules\Message\Models\Chat;
use App\Modules\Message\Models\Message;

class MessageRepository
{
    protected Chat $chat;

    public function __construct(
        Chat $chat
    ) {
        $this->chat = $chat;
    }
}
