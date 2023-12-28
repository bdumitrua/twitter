<?php

namespace App\Modules\Message\Resources;

use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'interlocutorId' => $this->interlocutorId,
            'interlocutorData' => new ShortUserResource($this->interlocutorData),
            'updated_at' => $this->updated_at->toW3cString(),
        ];
    }
}
