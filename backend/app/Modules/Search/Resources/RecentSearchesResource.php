<?php

namespace App\Modules\Search\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecentSearchesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $recentSearches = RecentSearchResource::collection($this->resource)->resolve();

        $actions = (array) ActionsResource::collection([
            [
                "ClearRecentSearches",
                "clearAuthorizedUserRecentSearches",
            ],
        ]);

        return [
            'recentSearches' => $recentSearches,
            'actions' => $actions
        ];
    }
}
