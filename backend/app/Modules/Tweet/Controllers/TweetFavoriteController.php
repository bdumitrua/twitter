<?php

namespace App\Modules\Tweet\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Services\TweetFavoriteService;
use Illuminate\Http\JsonResponse;

class TweetFavoriteController extends Controller
{
    private $tweetFavoriteService;

    public function __construct(TweetFavoriteService $tweetFavoriteService)
    {
        $this->tweetFavoriteService = $tweetFavoriteService;
    }

    public function index(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->tweetFavoriteService->index();
        });
    }

    public function add(Tweet $tweet): JsonResponse
    {
        return $this->handleServiceCall(function () use ($tweet) {
            return $this->tweetFavoriteService->add($tweet);
        });
    }

    public function remove(Tweet $tweet): JsonResponse
    {
        return $this->handleServiceCall(function () use ($tweet) {
            return $this->tweetFavoriteService->remove($tweet);
        });
    }
}
