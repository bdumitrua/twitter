<?php

namespace App\Modules\Search\Repositories;

use App\Modules\Search\DTO\RecentSearchDTO;
use App\Modules\Search\Models\RecentSearch;
use App\Modules\Search\Models\Search;
use Illuminate\Database\Eloquent\Collection;

class RecentSearchRepository
{
    protected RecentSearch $recentSearch;

    public function __construct(RecentSearch $recentSearch)
    {
        $this->recentSearch = $recentSearch;
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->recentSearch->with('linked_user')
            ->where('user_id', $userId)
            ->take(10)
            ->get();
    }

    public function create(RecentSearchDTO $recentSearchDTO): void
    {
        $data = $recentSearchDTO->toArray();
        $data = array_filter($data, fn ($value) => !is_null($value));

        $this->recentSearch->create($data);
    }
}
