<?php

namespace App\Modules\Search\Repositories;

use App\Modules\Search\DTO\RecentSearchDTO;
use App\Modules\Search\Models\RecentSearch;
use App\Modules\Search\Models\Search;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RecentSearchRepository
{
    protected RecentSearch $recentSearch;

    public function __construct(RecentSearch $recentSearch)
    {
        $this->recentSearch = $recentSearch;
    }

    protected function queryByLinkedId(int $authorizedUserId, int $linkedUserId): Builder
    {
        return $this->recentSearch->newQuery()
            ->where('user_id', $authorizedUserId)
            ->where('linked_user_id', $linkedUserId);
    }

    protected function queryByText(int $authorizedUserId, string $text): Builder
    {
        return $this->recentSearch->newQuery()
            ->where('user_id', $authorizedUserId)
            ->where('text', $text);
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->recentSearch->with('linked_user')
            ->where('user_id', $userId)
            ->latest('updated_at')
            ->take(10)
            ->get();
    }

    public function create(RecentSearchDTO $recentSearchDTO): void
    {
        $oldRecentSearch = null;
        if (!empty($recentSearchDTO->linkedUserId)) {
            $oldRecentSearch = $this->queryByLinkedId(
                $recentSearchDTO->userId,
                $recentSearchDTO->linkedUserId
            )->first();
        } else {
            $oldRecentSearch = $this->queryByText(
                $recentSearchDTO->userId,
                $recentSearchDTO->text
            )->first();
        }

        $data = array_filter($recentSearchDTO->toArray(), fn ($value) => !is_null($value));
        if (empty($oldRecentSearch)) {
            $this->recentSearch->create($data);
        } else {
            $oldRecentSearch->update($data);
        }
    }

    public function clear(int $authorizedUserId): void
    {
        $this->recentSearch->where('user_id', $authorizedUserId)->delete();
    }
}
