<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;

class NewSubscribtionsListener
{
    public function handle($event)
    {
        $userSubscribtion = $event->userSubscribtion;
        new KafkaProducer('new_subscribtions', $userSubscribtion);
    }
}
