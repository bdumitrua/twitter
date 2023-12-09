<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Log;

class DeletedUsersListsListener
{
    public function handle($event)
    {
        $usersList = $event->usersList;
        $topic = 'deleted_users_lists';

        Log::info("Creating message in {$topic} topic", $usersList);
        new KafkaProducer($topic, $usersList);
    }
}
