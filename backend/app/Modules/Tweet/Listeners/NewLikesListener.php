<?php

namespace App\Modules\Tweet\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewLikesListener
{
    protected KafkaProducer $kafkaProducer;

    public function __construct(KafkaProducer $kafkaProducer)
    {
        $this->kafkaProducer = $kafkaProducer;
    }

    public function handle($event)
    {
        $tweetLike = $event->tweetLike->toArray();
        $topic = 'newLikes';

        Log::info("Creating message in {$topic} topic", $tweetLike);
        $this->kafkaProducer->produce($topic, $tweetLike);
    }
}
