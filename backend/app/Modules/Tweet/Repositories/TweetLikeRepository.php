<?php

namespace App\Modules\Tweet\Repositories;

use App\Helpers\ResponseHelper;
use App\Modules\Tweet\Models\TweetLike;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

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
     * @return Response|null
     */
    public function remove(int $tweetId, int $userId): ?Response
    {
        $tweetLike = $this->queryByBothIds($tweetId, $userId)->first();
        $likeExists = !empty($tweetLike);

        if ($likeExists) {
            $tweetLike->delete();
        }

        return ResponseHelper::okResponse(!$likeExists);
    }
}
