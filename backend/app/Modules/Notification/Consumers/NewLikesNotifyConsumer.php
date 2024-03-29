<?php

namespace App\Modules\Notification\Consumers;

use App\Kafka\BaseConsumer;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Tweet\Models\Tweet;
use Illuminate\Log\LogManager;

class NewLikesNotifyConsumer extends BaseConsumer
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
            $tweetLike = $this->getMessageBody($message);

            if (!empty($tweetLike)) {
                $tweetId = $tweetLike->tweet_id;
                $likedUserId = $tweetLike->user_id;
                $likedTweetAuthor = Tweet::find($tweetId)->user_id;

                $notificationDTO = new NotificationDTO();
                $notificationDTO->type = 'newLike';
                $notificationDTO->relatedTweetId = $tweetId;
                $notificationDTO->relatedUserId = $likedUserId;
                $notificationDTO->userId = $likedTweetAuthor;

                $this->notificationService->create($notificationDTO);
                $this->acknowledge($message);
            }
        }
    }
}
