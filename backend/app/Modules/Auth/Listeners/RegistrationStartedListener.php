<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Support\Facades\Log;

class RegistrationStartedListener
{
    public function handle($event)
    {
        $authRegistration = $event->authRegistration->toArray();
        $topic = 'new_registrations';

        Log::info("Creating message in {$topic} topic", $authRegistration);
        new KafkaProducer($topic, $authRegistration);
    }
}