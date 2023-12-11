<?php

namespace App\Modules\Tweet\Resources;

use App\Http\Resources\ActionsResource;
use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetResource extends JsonResource
{
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

    private function prepareActions(): object
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

        return ActionsResource::collection($actions);
    }
}
