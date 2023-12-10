<?php

namespace App\Modules\Tweet\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetNoticeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'link' => $this->link,
            'user_id' => $this->user_id,
        ];
    }
}
