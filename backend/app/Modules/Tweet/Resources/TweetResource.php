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
        // Подгружаем
        $author = new ShortUserResource($this->author);
        $notices = !empty((array)$this->notices)
            ? TweetNoticeResource::collection($this->notices)
            : [];

        // Может добавиться на этапе сборки
        $thread = !empty((array)$this->thread)
            ? new TweetResource($this->thread)
            : [];

        // Только если подгружено ранее (в репозитории)
        $linkedTweet = $this->whenLoaded('linkedTweet', function () {
            return new TweetResource($this->linkedTweet);
        }, []);
        $replies = $this->whenLoaded('replies', function () {
            return TweetResource::collection($this->replies);
        }, []);

        $relatedData = $this->prepareRelatedData($linkedTweet, $thread);
        $counters = $this->prepareCounters();

        return [
            'id' => $this->id,
            'type' => $this->type,
            'author' => $author,
            'content' => [
                'text' => $this->text,
                'notices' => $notices,
                'created_at' => $this->created_at,
            ],
            'counters' => $counters,
            'related' => $relatedData,
            'replies' => $replies
        ];
    }

    private function prepareRelatedData($linkedTweet, $thread): array
    {
        if (!empty($linkedTweet) || !empty($thread)) {
            $relatedType = empty($linkedTweet) ? 'thread' : 'tweet';
            return [
                $relatedType => empty($linkedTweet) ? $thread : $linkedTweet
            ];
        }
        return [];
    }

    private function prepareCounters(): array
    {
        return [
            'likes' => [
                'count' => $this->likes_count
            ],
            'replies' => [
                'count' => $this->replies_count
            ],
            'reposts' => [
                'count' => $this->reposts_count
            ],
            'quotes' => [
                'count' => $this->quotes_count
            ],
            'favorites' => [
                'count' => $this->favorites_count
            ],
        ];
    }
}
