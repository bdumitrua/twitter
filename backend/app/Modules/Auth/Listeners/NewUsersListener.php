<?php

namespace App\Modules\Auth\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewUsersListener
{
    protected KafkaProducer $kafkaProducer;

    public function __construct(KafkaProducer $kafkaProducer)
    {
        $this->kafkaProducer = $kafkaProducer;
    }
    public function handle($event)
    {
        $user = $event->user->toArray();
        $topic = 'newUsers';

        Log::info("Creating message in {$topic} topic", $user);
        $this->kafkaProducer->produce($topic, $user);
    }
}
