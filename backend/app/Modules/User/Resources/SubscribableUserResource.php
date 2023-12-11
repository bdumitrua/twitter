<?php

namespace App\Modules\User\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscribableUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $actions = ActionsResource::collection([
            [
                "SubscribeOnUser",
                "subscribeOnUser",
                ["user" => $this->id]
            ],
            [
                "UnsubscribeFromUser",
                "unsubscribeFromUser",
                ["user" => $this->id]
            ]
        ]);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'about' => $this->about,
            'link' => $this->link,
            'avatar' => $this->avatar,
            'actions' => $actions
        ];
    }
}
