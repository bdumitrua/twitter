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
                "subscribeToUsersList",
                ["usersList" => $this->id]
            ],
            [
                "UnsubscribeFromList",
                "unsubscribeFromUsersList",
                ["usersList" => $this->id]
            ],
            [
                'GetUserMembers',
                'getUsersListMembers',
                ["usersList" => $this->id]
            ],
            [
                'GetUserSubscribers',
                'getUsersListSubscribers',
                ["usersList" => $this->id]
            ],
        ];

        $authorizedUserId = Auth::id();
        if ($this->user_id === $authorizedUserId) {
            $actionsArray[] = [
                "UpdateUsersList",
                "updateUsersList",
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
            'members_count' => $this->members_count ?? 0,
            'subscribers_count' => $this->subscribers_count ?? 0,
            'actions' => $actions
        ];
    }
}
