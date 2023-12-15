<?php

namespace App\Modules\Tweet\Services;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Repositories\TweetLikeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TweetLikeService
{
    private TweetLikeRepository $tweetLikeRepository;
    protected ?int $authorizedUserId;

    public function __construct(
        TweetLikeRepository $tweetLikeRepository
    ) {
        $this->tweetLikeRepository = $tweetLikeRepository;
        $this->authorizedUserId = Auth::id();
    }

    /**
     * @param Tweet $tweet
     * 
     * @return Response
     */
    public function add(Tweet $tweet): Response
    {
        return $this->tweetLikeRepository->add($tweet->id, $this->authorizedUserId);
    }


    /**
     * @param Tweet $tweet
     * 
     * @return Response
     */
    public function remove(Tweet $tweet): Response
    {
        return $this->tweetLikeRepository->remove($tweet->id, $this->authorizedUserId);
    }
}
