<?php

namespace App\Modules\Tweet\Resources;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Resources\ShortUserResource;
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
        $notices = empty((array)$this->notices) ? [] : TweetNoticeResource::collection($this->notices);
        $author = new ShortUserResource($this->author);
        $thread = empty((array)$this->thread) ? [] : new TweetResource($this->thread);
        $replies = $this->whenLoaded('replies', function () {
            return TweetResource::collection($this->replies);
        });
        $linkedTweet = $this->whenLoaded('linkedTweet', function () {
            return new TweetResource($this->linkedTweet);
        });

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'text' => $this->text,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'likes_count' => $this->likes_count,
            'favorites_count' => $this->favorites_count,
            'reposts_count' => $this->reposts_count,
            'replies_count' => $this->replies_count,
            'quotes_count' => $this->quotes_count,
            'notices' => $notices,
            'author' => $author,
            'linkedTweet' => $linkedTweet,
            'thread' => $thread,
            'replies' => $replies
        ];
    }
}
