<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewLikesListener
{
    public function handle($event)
    {
        $tweetLike = $event->tweetLike->toArray();
        $topic = 'new_likes';

        Log::info("Creating message in {$topic} topic", $tweetLike);
        new KafkaProducer($topic, $tweetLike);
    }
}
