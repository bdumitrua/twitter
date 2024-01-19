<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewSubscribtionsListener
{
    protected KafkaProducer $kafkaProducer;

    public function __construct(KafkaProducer $kafkaProducer)
    {
        $this->kafkaProducer = $kafkaProducer;
    }

    public function handle($event)
    {
        $userSubscribtion = $event->userSubscribtion->toArray();
        $topic = 'newSubscribtions';

        Log::info("Creating message in {$topic} topic", $userSubscribtion);
        $this->kafkaProducer->produce($topic, $userSubscribtion);
    }
}
