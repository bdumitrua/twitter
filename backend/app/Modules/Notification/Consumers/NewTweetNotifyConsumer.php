<?php

namespace App\Modules\Notification\Consumers;

use App\Kafka\BaseConsumer;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\User\Models\User;
use Illuminate\Log\LogManager;

class NewTweetNotifyConsumer extends BaseConsumer
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
            $tweetData = $this->getMessageBody($message);

            if (!empty($tweetData)) {
                $tweetId = $tweetData->id;
                $userWithSubscribers = User::find($tweetData->user_id)->with(['subscribers'])->first();
                $subscrubersIds = $userWithSubscribers->subscribers->pluck('subscriber_id');

                foreach ($subscrubersIds as $subscriberId) {
                    $notificationDTO = new NotificationDTO();
                    $notificationDTO->type = 'newTweet';
                    $notificationDTO->relatedId = $tweetId;
                    $notificationDTO->userId = $subscriberId;

                    $this->notificationService->create($notificationDTO);
                }

                $this->acknowledge($message);
            }
        }
    }
}
