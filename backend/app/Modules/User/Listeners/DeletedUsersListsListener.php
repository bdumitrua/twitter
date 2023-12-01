<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;

class DeletedUsersListsListener
{
    public function handle($event)
    {
        $usersList = $event->usersList;
        new KafkaProducer('deleted_users_lists', $usersList);
    }
}
