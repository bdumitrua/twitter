<?php

namespace App\Modules\Auth\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class PasswordResetStartedListener
{
    protected KafkaProducer $kafkaProducer;

    public function __construct(KafkaProducer $kafkaProducer)
    {
        $this->kafkaProducer = $kafkaProducer;
    }
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
        $this->kafkaProducer->produce($topic, $mergedData);
    }
}
