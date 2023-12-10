<?php

namespace App\Modules\Tweet\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetDraftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
        ];
    }
}
