<?php

namespace App\Modules\User\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UsersListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $actionsArray = [
            [
                "SubscribeOnList",
                "subscribe_to_users_list",
                ["usersList" => $this->id]
            ],
            [
                "UnsubscribeFromList",
                "unsubscribe_from_users_list",
                ["usersList" => $this->id]
            ],
            [
                'GetUserMembers',
                'get_users_list_members',
                ["usersList" => $this->id]
            ],
            [
                'GetUserSubscribers',
                'get_users_list_subscribers',
                ["usersList" => $this->id]
            ],
        ];

        $authorizedUserId = Auth::id();
        if ($this->user_id === $authorizedUserId) {
            $actionsArray[] = [
                "UpdateUsersList",
                "update_users_list",
                ["usersList" => $this->id]
            ];
        }

        $actions = ActionsResource::collection($actionsArray);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'description' => $this->description,
            'bg_image' => $this->bg_image,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_private' => $this->is_private,
            'members_count' => $this->members_count ?? 0,
            'subscribers_count' => $this->subscribers_count ?? 0,
            'actions' => $actions
        ];
    }
}
