<?php

namespace App\Modules\Auth\Events;

use App\Modules\Auth\Models\AuthRegistration;
use App\Modules\User\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RegistrationStartedEvent
{
    public $authRegistration;

    public function __construct(
        AuthRegistration $authRegistration,
    ) {
        $this->authRegistration = $authRegistration;
    }
}
