<?php

namespace App\Modules\Notification\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    // TODO DATA
    // Прикрепить свзанную сущность
    public function toArray(Request $request): array
    {
        $allStatuses = ['unread', 'sended', 'readed'];
        $status = $this->status;
        $availableStatuses = [];
        $actions = [];

        $statusIndex = array_search($status, $allStatuses);
        if ($statusIndex !== false) {
            $availableStatuses = array_slice($allStatuses, $statusIndex + 1);
        }

        if (!empty($availableStatuses)) {
            $actions = (array) ActionsResource::collection([
                [
                    "UpdateNotificationStatus",
                    "updateNotification",
                    ["notification" => $this->uuid],
                    ['available_statuses' => $availableStatuses]
                ],
            ]);
        }

        return [
            'uuid' => $this->uuid,
            'userId' => $this->user_id,
            'type' => $this->type,
            'relatedId' => $this->related_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'actions' => $actions,
        ];
    }
}
