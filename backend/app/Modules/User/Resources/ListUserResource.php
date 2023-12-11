<?php

namespace App\Modules\User\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $actions = ActionsResource::collection([
            [
                "AddUserToList",
                "add_member_to_users_list",
                [
                    "user" => $this->id,
                    "usersList" => $this->users_list_id,
                ]
            ],
            [
                "RemoveUserFromList",
                "remove_member_from_users_list",
                [
                    "user" => $this->id,
                    "usersList" => $this->users_list_id,
                ]
            ],
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
