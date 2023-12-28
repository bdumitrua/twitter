<?php

namespace App\Modules\Notification\Resources;

use App\Http\Resources\ActionsResource;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Resources\TweetResource;
use App\Modules\User\Models\User;
use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->resource = (object) $this->resource;

        $actions = $this->assembleActions();
        $relatedData = $this->getRelatedData();

        return [
            'uuid' => $this->uuid,
            'userId' => $this->userId,
            'type' => $this->type,
            'status' => $this->status,
            'relatedData' => $relatedData,
            'created_at' => date('Y-m-d H:i:s', $this->created_at / 1000),
            'actions' => $actions,
        ];
    }

    protected function getRelatedData(): ?array
    {
        if (empty($this->relatedData)) {
            return null;
        }

        if ($this->relatedData instanceof Tweet) {
            return (new TweetResource($this->relatedData))->resolve();
        }

        if ($this->relatedData instanceof User) {
            return (new ShortUserResource($this->relatedData))->resolve();
        }

        if (isset($this->relatedData['user']) || isset($this->relatedData['tweet'])) {
            return [
                'tweet' => (new TweetResource($this->relatedData['tweet']))->resolve(),
                'user' => (new ShortUserResource($this->relatedData['user']))->resolve(),
            ];
        }

        return null;
    }

    protected function assembleActions(): array
    {
        $actions = [];
        if ($this->status === 'unread') {
            $actions[] = new ActionsResource([
                "ReadNotification",
                "readNotification",
                ["notificationUuid" => $this->uuid],
            ]);
        }

        $actions[] = new ActionsResource([
            "DeleteNotification",
            "deleteNotification",
            ["notificationUuid" => $this->uuid],
        ]);

        return $actions;
    }
}
