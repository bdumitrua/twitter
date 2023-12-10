<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewUsersListener
{
    public function handle($event)
    {
        $user = $event->user->toArray();
        $topic = 'new_users';

        Log::info("Creating message in {$topic} topic", $user);
        new KafkaProducer($topic, $user);
    }
}
