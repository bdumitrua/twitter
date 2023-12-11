<?php

namespace App\Modules\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Только если подгружено ранее (в репозитории)
        $membersData = $this->whenLoaded('members_data', function () {
            return GroupUserResource::collection($this->members_data);
        }, []);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'members_count' => $this->members_count ?? 0,
            'members_data' => $membersData,
        ];
    }
}
