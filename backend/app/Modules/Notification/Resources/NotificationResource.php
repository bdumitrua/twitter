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
        $this->resource = (object) $this->resource;

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
            'userId' => $this->userId,
            'type' => $this->type,
            'relatedId' => $this->relatedId ?? null,
            'status' => $this->status,
            'created_at' => date('Y-m-d H:i:s', $this->created_at / 1000),
            'actions' => $actions,
        ];
    }
}
