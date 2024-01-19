<?php

namespace App\Modules\Tweet\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewTweetsListener
{
    protected KafkaProducer $kafkaProducer;

    public function __construct(KafkaProducer $kafkaProducer)
    {
        $this->kafkaProducer = $kafkaProducer;
    }

    public function handle($event)
    {
        $tweet = $event->tweet->toArray();
        $topic = 'newTweets';

        Log::info("Creating message in {$topic} topic", $tweet);
        $this->kafkaProducer->produce($topic, $tweet);
    }
}
