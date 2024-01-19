<?php

namespace App\Modules\Auth\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class RegistrationStartedListener
{
    protected KafkaProducer $kafkaProducer;

    public function __construct(KafkaProducer $kafkaProducer)
    {
        $this->kafkaProducer = $kafkaProducer;
    }

    public function handle($event)
    {
        $authRegistration = $event->authRegistration->toArray();
        $topic = 'newRegistrations';

        Log::info("Creating message in {$topic} topic", $authRegistration);
        $this->kafkaProducer->produce($topic, $authRegistration);
    }
}
