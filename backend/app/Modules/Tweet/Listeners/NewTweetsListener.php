<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewTweetsListener
{
    public function handle($event)
    {
        $tweet = $event->tweet->toArray();
        $topic = 'new_tweets';

        Log::info("Creating message in {$topic} topic", $tweet);
        new KafkaProducer($topic, $tweet);
    }
}
