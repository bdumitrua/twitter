<?php

namespace App\Modules\Search\Resources;

use App\Http\Resources\ActionsResource;
use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecentSearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $linkedUser = $this->whenLoaded('linked_user', function () {
            return new ShortUserResource($this->linked_user);
        }, []);

        return [
            'id' => $this->id,
            'text' => $this->text,
            'linked_user' => $linkedUser,
            "updated_at" => $this->updated_at,
        ];
    }
}
