<?php

namespace App\Modules\Tweet\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TweetDraftsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tweetDrafts = TweetDraftResource::collection($this->resource);

        $actions = (array) ActionsResource::collection([
            [
                "DeleteTweetDrafts",
                "deleteTweetDrafts",
            ],
        ]);

        return [
            'tweetDrafts' => $tweetDrafts,
            'actions' => $actions,
        ];
    }
}
