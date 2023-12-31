<?php

namespace App\Modules\User\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userData = $this->resource->usersData;
        $groupId = $this->resource->user_group_id;
        $actions = (array) ActionsResource::collection([
            [
                "AddUserToGroup",
                "addUserToUserGroup",
                [
                    "user" => $userData->id,
                    "userGroup" => $groupId,
                ]
            ],
            [
                "RemoveUserFromGroup",
                "removeUserFromUserGroup",
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
