<?php

namespace App\Modules\Tweet\Repositories;

use App\Modules\Tweet\Models\TweetFavorite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TweetFavoriteRepository
{
    protected $tweetFavorite;

    public function __construct(
        TweetFavorite $tweetFavorite,
    ) {
        $this->tweetFavorite = $tweetFavorite;
    }

    /**
     * @param int $tweetId
     * @param int $userId
     * 
     * @return Builder
     */
    protected function queryByBothIds(int $tweetId, int $userId): Builder
    {
        return $this->tweetFavorite->newQuery()->where([
            'tweet_id' => $tweetId,
            'user_id' => $userId,
        ]);
    }

    /**
     * @param int $userId
     * 
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        return $this->tweetFavorite->where('user_id', '=', $userId)->get();
    }

    /**
     * @param int $tweetId
     * @param int $userId
     * 
     * @return void
     */
    public function add(int $tweetId, int $userId): void
    {
        if (empty($this->queryByBothIds($tweetId, $userId)->first())) {
            $this->tweetFavorite->create([
                'tweet_id' => $tweetId,
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * @param int $tweetId
     * @param int $userId
     * 
     * @return void
     */
    public function remove(int $tweetId, int $userId): void
    {
        $tweetFavorite = $this->tweetFavorite->where([
            'tweet_id' => $tweetId,
            'user_id' => $userId,
        ])->first();

        if (!empty($tweetFavorite)) {
            $tweetFavorite->delete();
        }
    }
}
