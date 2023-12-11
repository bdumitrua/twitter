<?php

namespace App\Modules\Auth\Events;

use App\Modules\Auth\Models\AuthReset;
use App\Modules\User\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PasswordResetStartedEvent
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
}
