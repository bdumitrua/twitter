<?php

namespace App\Modules\Message\Repositories;

use App\Modules\Message\Models\Message;

class MessageRepository
{
    protected Message $message;

    public function __construct(
        Message $message
    ) {
        $this->message = $message;
    }
}
