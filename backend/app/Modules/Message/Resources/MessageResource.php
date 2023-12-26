<?php

namespace App\Modules\Message\Resources;

use App\Http\Resources\ActionsResource;
use App\Modules\Tweet\Resources\TweetResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->resource = (object) $this->resource;
        $entityData = $this->getEntityData();

        $actions = (array) ActionsResource::collection([
            [
                "ReadMessage",
                "readMessage",
                ["messageUuid" => $this->uuid]
            ],
            [
                "DeleteMessage",
                "deleteMessage",
                ["messageUuid" => $this->uuid]
            ]
        ]);

        return [
            'uuid' => $this->uuid,
            'text' => $this?->text ?? null,
            'status' => $this->status,
            'senderId' => $this->senderId,
            'created_at' => date('Y-m-d H:i:s', $this->created_at / 1000),
            'linkedEntityId' => $this->linkedEntityId ?? null,
            'linkedEntityType' => $this->linkedEntityType ?? null,
            'linkedEntityData' => $entityData,
            'actions' => $actions
        ];
    }

    protected function getEntityData(): ?array
    {
        if (empty($this->linkedEntityType) || empty($this->linkedEntityId)) {
            return null;
        }

        if ($this->linkedEntityType === 'tweet') {
            return (new TweetResource($this->linkedEntityData))->resolve();
        }
    }
}
