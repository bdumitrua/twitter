<?php

namespace App\Modules\Auth\Events;

use App\Modules\Auth\Models\AuthReset;
use App\Modules\User\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PasswordResetStartedEvent implements ShouldBroadcast
{
    public $authReset;
    public $email;

    public function __construct(
        AuthReset $authReset,
        string $email,
    ) {
        $this->authReset = $authReset;
        $this->email = $email;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
