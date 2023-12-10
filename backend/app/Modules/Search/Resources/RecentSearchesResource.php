<?php

namespace App\Modules\Search\Resources;

use App\Http\Resources\ActionsResource;
use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecentSearchesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $recentSearches = RecentSearchResource::collection($this->resource);

        $actions = ActionsResource::collection([
            [
                "ClearRecentSearches",
                "clear_authorized_user_recent_searches",
            ],
        ]);

        return [
            'recent_searches' => $recentSearches,
            'actions' => $actions
        ];
    }
}
