<?php

namespace App\Modules\Tweet\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetDraftsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $drafts = TweetDraftResource::collection($this->resource);

        $actions = ActionsResource::collection([
            [
                "DeleteTweetDrafts",
                "delete_tweet_drafts",
            ],
        ]);

        return [
            'tweet_drafts' => $drafts,
            'actions' => $actions,
        ];
    }
}
