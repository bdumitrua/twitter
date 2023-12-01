<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use App\Modules\Tweet\Models\TweetNotice;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;

class NewNoticeListener
{
    public function handle($event)
    {
        /** @var TweetNotice */
        $tweetNotice = $event->tweetNotice;
        new KafkaProducer('new_notices', $tweetNotice);
    }
}
