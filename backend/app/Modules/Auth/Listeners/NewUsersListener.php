<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;

class NewUsersListener
{
    public function handle($event)
    {
        $user = $event->user;
        new KafkaProducer('new_users', $user->toArray());
    }
}
