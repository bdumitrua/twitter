<?php

namespace App\Modules\Tweet\Repositories;

use App\Modules\Tweet\Models\TweetLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TweetLikeRepository
{
    protected TweetLike $tweetLike;
    protected TweetRepository $tweetRepository;

    public function __construct(
        TweetLike $tweetLike,
        TweetRepository $tweetRepository,
    ) {
        $this->tweetLike = $tweetLike;
        $this->tweetRepository = $tweetRepository;
    }

    /**
     * @param int $tweetId
     * @param int $userId
     * 
     * @return Builder
     */
    protected function queryByBothIds(int $tweetId, int $userId): Builder
    {
        return $this->tweetLike->newQuery()->where([
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
        return $this->tweetLike->where('user_id', '=', $userId)->get();
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
            $this->tweetLike->create([
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
        $tweetLike = $this->queryByBothIds($tweetId, $userId)->first();

        if (!empty($tweetLike)) {
            $tweetLike->delete();
        }
    }
}
