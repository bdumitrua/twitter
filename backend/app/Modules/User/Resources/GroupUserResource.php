<?php

namespace App\Modules\User\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userData = $this->resource->users_data;
        $groupId = $this->resource->user_group_id;
        $actions = ActionsResource::collection([
            [
                "AddUserToGroup",
                "add_user_to_user_group",
                [
                    "user" => $userData->id,
                    "userGroup" => $groupId,
                ]
            ],
            [
                "RemoveUserFromGroup",
                "remove_user_from_user_group",
                [
                    "user" => $userData->id,
                    "userGroup" => $groupId,
                ]
            ]
        ]);

        return [
            'id' => $userData->id,
            'name' => $userData->name,
            'about' => $userData->about,
            'link' => $userData->link,
            'avatar' => $userData->avatar,
            'actions' => $actions
        ];
    }
}
