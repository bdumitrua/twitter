<?php

namespace App\Modules\Tweet\Services;

use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Repositories\TweetLikeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TweetLikeService
{
    private $tweetLikeRepository;

    public function __construct(
        TweetLikeRepository $tweetLikeRepository
    ) {
        $this->tweetLikeRepository = $tweetLikeRepository;
    }

    public function index(): Collection
    {
        return $this->tweetLikeRepository->getByUserId(Auth::id());
    }

    public function add(Tweet $tweet): void
    {
        $this->tweetLikeRepository->add($tweet->id, Auth::id());
    }

    public function remove(Tweet $tweet): void
    {
        $this->tweetLikeRepository->remove($tweet->id, Auth::id());
    }
}
