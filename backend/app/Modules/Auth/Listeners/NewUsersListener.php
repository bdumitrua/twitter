<?php

namespace App\Modules\Auth\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewUsersListener
{
    public function handle($event)
    {
        $user = $event->user->toArray();
        $topic = 'newUsers';

        Log::info("Creating message in {$topic} topic", $user);
        new KafkaProducer($topic, $user);
    }
}
