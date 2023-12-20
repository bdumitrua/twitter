<?php

namespace App\Modules\Tweet\Resources;

use App\Http\Resources\ActionsResource;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Всегда добавляется на этапе сборки
        $author = new ShortUserResource($this->author);

        // Может добавиться на этапе сборки
        $notices = !empty((array)$this->notices)
            ? TweetNoticeResource::collection($this->notices)
            : [];

        $thread = !empty((array)$this->thread)
            ? new TweetResource($this->thread)
            : [];

        $linkedTweetData = !empty((array)$this->linkedTweetData)
            ? new TweetResource($this->linkedTweetData)
            : [];

        $replies = !empty((array)$this->replies)
            ? TweetResource::collection($this->replies)
            : [];

        $relatedData = $this->prepareRelatedData($linkedTweetData, $thread) ?? [];
        $counters = $this->prepareCounters();
        $actions = $this->prepareActions();

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
            'replies' => $replies,
            'actions' => $actions,
        ];
    }

    private function prepareRelatedData($linkedTweet, $thread): ?TweetResource
    {
        if (!empty($linkedTweet) || !empty($thread)) {
            return empty($linkedTweet) ? $thread : $linkedTweet;
        }

        return null;
    }

    private function prepareCounters(): array
    {
        return [
            'likes' => [
                'count' => $this->likes_count,
                'active' => $this->isLiked ?? false
            ],
            'replies' => [
                'count' => $this->replies_count
            ],
            'reposts' => [
                'count' => $this->reposts_count,
                'active' => $this->isReposted ?? false
            ],
            'quotes' => [
                'count' => $this->quotes_count
            ],
            'favorites' => [
                'count' => $this->favorites_count,
                'active' => $this->isFavorite ?? false
            ],
        ];
    }

    private function prepareActions(): array
    {
        $isShowing = $this->whenLoaded('replies', function () {
            return true;
        }, false);

        $actions = [
            [
                "LikeTweet",
                "likeTweet",
                ["tweet" => $this->id]
            ],
            [
                "DislikeTweet",
                "dislikeTweet",
                ["tweet" => $this->id]
            ],
            [
                "BookmarkTweet",
                "addTweetToBookmarks",
                ["tweet" => $this->id]
            ],
            [
                "UnbookmarkTweet",
                "removeTweetFromBookmarks",
                ["tweet" => $this->id]
            ],
            [
                "RepostTweet",
                "createTweet",
            ],
            [
                "UnrepostTweet",
                "unrepostTweet",
                ["tweet" => $this->id]
            ],
            [
                "QuoteTweet",
                "createTweet",
            ],
        ];

        if ($isShowing) {
            $actions[] = [
                'ShareTweet',
                'getTweetById',
                ["tweet" => $this->id]
            ];
        } else {
            $actions[] = [
                'ShowTweet',
                'getTweetById',
                ["tweet" => $this->id]
            ];
        }

        return (array) ActionsResource::collection($actions);
    }
}
