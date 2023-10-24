<?php

namespace App\Modules\Tweet\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Services\TweetLikeService;
use Illuminate\Http\JsonResponse;

class TweetLikeController extends Controller
{
    private $tweetLikeService;

    public function __construct(TweetLikeService $tweetLikeService)
    {
        $this->tweetLikeService = $tweetLikeService;
    }

    public function index(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->tweetLikeService->index();
        });
    }

    public function add(Tweet $tweet): JsonResponse
    {
        return $this->handleServiceCall(function () use ($tweet) {
            return $this->tweetLikeService->add($tweet);
        });
    }

    public function remove(Tweet $tweet): JsonResponse
    {
        return $this->handleServiceCall(function () use ($tweet) {
            return $this->tweetLikeService->remove($tweet);
        });
    }
}
