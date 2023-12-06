<?php

namespace App\Modules\Search\Resources;

use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecentSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $linkedUser = empty($this->linked_user) ? [] : new ShortUserResource($this->linked_user);

        return [
            'id' => $this->id,
            'text' => $this->text,
            'linked_user' => $linkedUser,
            "updated_at" => $this->updated_at,
        ];
    }
}
