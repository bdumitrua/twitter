<?php

namespace App\Modules\Tweet\Repositories;

use App\Modules\Tweet\Models\TweetLike;
use App\Modules\User\Events\TweetLikeEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TweetLikeRepository
{
    protected $tweetLike;

    public function __construct(
        TweetLike $tweetLike,
    ) {
        $this->tweetLike = $tweetLike;
    }

    protected function queryByBothIds(int $tweetId, int $userId): Builder
    {
        return $this->tweetLike->newQuery()->where([
            'tweet_id' => $tweetId,
            'user_id' => $userId,
        ]);
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->tweetLike
            ->where('user_id', '=', $userId)
            ->get();
    }

    public function add(int $tweetId, int $userId): void
    {
        if (empty($this->queryByBothIds($tweetId, $userId)->first())) {
            $tweetLike = $this->tweetLike->create([
                'tweet_id' => $tweetId,
                'user_id' => $userId,
            ]);
        }
    }

    public function remove(int $tweetId, int $userId): void
    {
        $tweetLike = $this->tweetLike->where([
            'tweet_id' => $tweetId,
            'user_id' => $userId,
        ])->first();

        if (!empty($tweetLike)) {
            $tweetLike->delete();
        }
    }
}
