<?php

namespace App\Modules\Tweet\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class NewNoticeListener
{
    protected KafkaProducer $kafkaProducer;

    public function __construct(KafkaProducer $kafkaProducer)
    {
        $this->kafkaProducer = $kafkaProducer;
    }

    public function handle($event)
    {
        $tweetNotice = $event->tweetNotice->toArray();
        $topic = 'newNotices';

        Log::info("Creating message in {$topic} topic", $tweetNotice);
        $this->kafkaProducer->produce($topic, $tweetNotice);
    }
}
