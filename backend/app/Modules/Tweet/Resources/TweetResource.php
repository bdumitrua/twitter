<?php

namespace App\Modules\Tweet\Resources;

use App\Modules\User\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $thread = $this->thread ? new TweetResource($this->thread) : [];

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'text' => $this->text,
            'type' => $this->type,
            'linked_tweet_id' => $this->linked_tweet_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'likes_count' => $this->likes_count,
            'favorites_count' => $this->favorites_count,
            'reposts_count' => $this->reposts_count,
            'replies_count' => $this->replies_count,
            'quotes_count' => $this->quotes_count,
            'author' => new UserResource($this->whenLoaded('author')),
            'thread' => $thread
        ];
    }
}
