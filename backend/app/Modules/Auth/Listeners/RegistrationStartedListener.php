<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class RegistrationStartedListener
{
    public function handle($event)
    {
        $authRegistration = $event->authRegistration->toArray();
        $topic = 'newRegistrations';

        Log::info("Creating message in {$topic} topic", $authRegistration);
        new KafkaProducer($topic, $authRegistration);
    }
}
