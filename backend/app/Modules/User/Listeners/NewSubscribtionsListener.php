<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Support\Facades\Log;

class NewSubscribtionsListener
{
    public function handle($event)
    {
        $userSubscribtion = $event->userSubscribtion->toArray();
        $topic = 'new_subscribtions';

        Log::info("Creating message in {$topic} topic", $userSubscribtion);
        new KafkaProducer($topic, $userSubscribtion);
    }
}
