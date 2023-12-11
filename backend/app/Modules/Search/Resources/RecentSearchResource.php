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
        $linkedUser = $this->whenLoaded('linkedUser', function () {
            return new ShortUserResource($this->linkedUser);
        }, []);

        return [
            'id' => $this->id,
            'text' => $this->text,
            'linkedUser' => $linkedUser,
            "updated_at" => $this->updated_at,
        ];
    }
}
