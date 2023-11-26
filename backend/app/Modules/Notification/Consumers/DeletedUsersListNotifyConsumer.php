<?php

namespace App\Modules\Notification\Consumers;

use App\Kafka\BaseConsumer;

class DeletedUsersListNotifyConsumer extends BaseConsumer
{
    public function consume(): void
    {
        // 
    }
}
