<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewTweetsListener
{
    public function handle($event)
    {
        $tweet = $event->tweet->toArray();
        $topic = 'newTweets';

        Log::info("Creating message in {$topic} topic", $tweet);
        new KafkaProducer($topic, $tweet);
    }
}
