<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewNoticeListener
{
    public function handle($event)
    {
        $tweetNotice = $event->tweetNotice->toArray();
        $topic = 'newNotices';

        Log::info("Creating message in {$topic} topic", $tweetNotice);
        new KafkaProducer($topic, $tweetNotice);
    }
}
