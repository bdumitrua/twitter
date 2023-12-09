<?php

namespace App\Modules\Search\Repositories;

use App\Modules\Search\DTO\RecentSearchDTO;
use App\Modules\Search\Models\RecentSearch;
use App\Modules\Search\Models\Search;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RecentSearchRepository
{
    use GetCachedData;

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
            ->where('text', $text)
            ->whereNull('linked_user_id');
    }

    public function getByUserId(int $userId, $updateCache = false): Collection
    {
        $cacheKey = KEY_USER_SEARCH . $userId;
        return $this->getCachedData($cacheKey, null, function () use ($userId) {
            return $this->recentSearch->with('linked_user')
                ->where('user_id', $userId)
                ->latest('updated_at')
                ->take(10)
                ->get();
        }, $updateCache);
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

        if (empty($oldRecentSearch)) {
            $data = array_filter($recentSearchDTO->toArray(), fn ($value) => !is_null($value));
            $this->recentSearch->create($data);
        } else {
            $oldRecentSearch->text = $recentSearchDTO->text;
            $oldRecentSearch->updated_at = now();
            $oldRecentSearch->save();
        }

        $this->clearUserRecentCache($recentSearchDTO->userId);
    }

    public function clear(int $authorizedUserId): void
    {
        $this->recentSearch->where('user_id', $authorizedUserId)->delete();
        $this->clearUserRecentCache($authorizedUserId);
    }

    protected function clearUserRecentCache(int $userId): void
    {
        $cacheKey = KEY_USER_SEARCH . $userId;
        $this->clearCache($cacheKey);
    }
}
