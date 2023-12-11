<?php

namespace App\Modules\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Только если подгружено ранее (в репозитории)
        $membersData = $this->whenLoaded('membersData', function () {
            return GroupUserResource::collection($this->membersData);
        }, []);

        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'membersCount' => $this->members_count ?? 0,
            'membersData' => $membersData,
        ];
    }
}
