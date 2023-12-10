<?php

namespace App\Modules\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
        ];
    }
}
