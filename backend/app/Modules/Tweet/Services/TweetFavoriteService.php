<?php

namespace App\Modules\Tweet\Services;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Repositories\TweetFavoriteRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TweetFavoriteService
{
    private TweetFavoriteRepository $tweetFavoriteRepository;
    protected ?int $authorizedUserId;

    public function __construct(
        TweetFavoriteRepository $tweetFavoriteRepository,
    ) {
        $this->tweetFavoriteRepository = $tweetFavoriteRepository;
        $this->authorizedUserId = Auth::id();
    }

    /**
     * @param Tweet $tweet
     * 
     * @return Response
     */
    public function add(Tweet $tweet): Response
    {
        return $this->tweetFavoriteRepository->add($tweet->id, $this->authorizedUserId);
    }

    /**
     * @param Tweet $tweet
     * 
     * @return Response
     */
    public function remove(Tweet $tweet): Response
    {
        return $this->tweetFavoriteRepository->remove($tweet->id, $this->authorizedUserId);
    }
}
