<?php

namespace App\Modules\Auth\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class PasswordResetStartedListener
{
    public function handle($event)
    {
        $authReset = $event->authReset->toArray();
        $email = $event->email;
        $topic = 'passwordResets';

        $mergedData = array_merge(
            $authReset,
            ['email' => $email]
        );

        Log::info("Creating message in {$topic} topic", $mergedData);
        new KafkaProducer($topic, $mergedData);
    }
}
