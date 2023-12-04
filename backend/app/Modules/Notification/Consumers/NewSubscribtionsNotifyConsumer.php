<?php

namespace App\Modules\Notification\Consumers;

use App\Kafka\BaseConsumer;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Services\NotificationService;
use Illuminate\Log\LogManager;

class NewSubscribtionsNotifyConsumer extends BaseConsumer
{
    protected $notificationService;

    public function __construct(
        string $topicName,
        string $consumerGroup,
        NotificationService $notificationService,
        LogManager $logger,
    ) {
        parent::__construct($topicName, $consumerGroup);

        $this->notificationService = $notificationService;
        $this->logger = $logger;
    }

    public function consume(): void
    {
        while (true) {
            $message = $this->consumer->receive();
            $userSubscribtion = $this->getMessageBody($message);

            if (!empty($userSubscribtion)) {
                $notificationDTO = new NotificationDTO();
                $notificationDTO->type = 'new_subscribtions';
                $notificationDTO->relatedId = $userSubscribtion->subscriber_id;
                $notificationDTO->userId = $userSubscribtion->user_id;

                $this->notificationService->create($notificationDTO);
                $this->acknowledge($message);
            }
        }
    }
}
