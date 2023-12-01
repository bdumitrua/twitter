<?php

namespace App\Modules\Notification\Consumers;

use App\Kafka\BaseConsumer;
use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListSubscribtion;

class DeletedUsersListNotifyConsumer extends BaseConsumer
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
            $usersList = $this->getMessageBody($message);

            if (!empty($usersList)) {
                $usersListId = $usersList->id;
                $subscribersIds = UsersListSubscribtion::where('users_list_id', $usersList->id)->get(['user_id'])->pluck('user_id')->toArray();

                foreach ($subscribersIds as $subscriberId) {
                    $notificationDTO = new NotificationDTO();
                    $notificationDTO->type = 'deleted_users_lists';
                    $notificationDTO->relatedId = $usersListId;
                    $notificationDTO->userId = $subscriberId;

                    $this->notificationService->create($notificationDTO);
                }

                $this->acknowledge($message);
            }
        }
    }
}
