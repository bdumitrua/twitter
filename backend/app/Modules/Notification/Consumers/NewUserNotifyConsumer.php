<?php

namespace App\Modules\Notification\Consumers;

use App\Kafka\BaseConsumer;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Services\NotificationService;
use Enqueue\RdKafka\RdKafkaConnectionFactory;

class NewUserNotifyConsumer extends BaseConsumer
{
    protected $notificationService;

    public function __construct(
        string $topicName,
        string $consumerGroup,
        NotificationService $notificationService,
    ) {
        parent::__construct($topicName, $consumerGroup);

        $this->notificationService = $notificationService;
    }

    public function consume(): void
    {
        while (true) {
            $message = $this->consumer->receive();
            $user = $this->getMessageBody($message);

            if (!empty($user)) {
                // Future
            }
        }
    }
}
