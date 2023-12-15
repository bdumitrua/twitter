<?php

namespace App\Modules\Tweet\Repositories;

use App\Helpers\ResponseHelper;
use App\Modules\Tweet\Models\TweetFavorite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

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
     * @return TweetFavorite|null
     */
    public function getByBothIds(int $tweetId, int $userId): ?TweetFavorite
    {
        return $this->queryByBothIds($tweetId, $userId)->first();
    }

    /**
     * @param int $tweetId
     * @param int $userId
     * 
     * @return Response
     */
    public function add(int $tweetId, int $userId): Response
    {
        $favoriteExists = $this->queryByBothIds($tweetId, $userId)->exists();
        if (!$favoriteExists) {
            $this->tweetFavorite->create([
                'tweet_id' => $tweetId,
                'user_id' => $userId,
            ]);
        }

        return ResponseHelper::okResponse(!$favoriteExists);
    }

    /**
     * @param int $tweetId
     * @param int $userId
     * 
     * @return Response
     */
    public function remove(int $tweetId, int $userId): Response
    {
        $tweetFavorite = $this->getByBothIds($tweetId, $userId);
        $favoriteExists = !empty($tweetFavorite);

        if ($favoriteExists) {
            $tweetFavorite->delete();
        }

        return ResponseHelper::okResponse($favoriteExists);
    }
}
