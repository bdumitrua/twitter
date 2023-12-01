<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;

class NewLikesListener
{
    public function handle($event)
    {
        $tweetLike = $event->tweetLike;
        new KafkaProducer('new_likes', $tweetLike->toArray());
    }
}
