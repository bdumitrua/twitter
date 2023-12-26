<?php

namespace App\Modules\Message\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewMessageEvent
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }
}
