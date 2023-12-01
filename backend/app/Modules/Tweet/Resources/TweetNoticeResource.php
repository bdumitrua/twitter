<?php

namespace App\Modules\Tweet\Resources;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Resources\ShortUserResource;
use App\Modules\User\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetNoticeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'link' => $this->link,
            'user_id' => $this->user_id,
            'tweet_id' => $this->tweet_id,
        ];
    }
}
