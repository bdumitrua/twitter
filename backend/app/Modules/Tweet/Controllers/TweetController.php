<?php

namespace App\Modules\Tweet\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Requests\CreateRepostRequest;
use App\Modules\Tweet\Requests\CreateThreadRequest;
use App\Modules\Tweet\Requests\TweetRequest;
use App\Modules\Tweet\Services\TweetService;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use Illuminate\Http\JsonResponse;

class TweetController extends Controller
{
    private $tweetService;

    public function __construct(TweetService $tweetService)
    {
        $this->tweetService = $tweetService;
    }

    public function feed(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->tweetService->feed();
        });
    }

    public function show(Tweet $tweet): JsonResponse
    {
        return $this->handleServiceCall(function () use ($tweet) {
            return $this->tweetService->show($tweet);
        });
    }

    public function user(User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->tweetService->user($user);
        });
    }

    public function replies(User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->tweetService->replies($user);
        });
    }

    // ! DOESN'T WORK
    public function media(User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->tweetService->media($user);
        });
    }

    public function likes(User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->tweetService->likes($user);
        });
    }

    public function bookmarks(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->tweetService->bookmarks();
        });
    }

    public function list(UsersList $usersList): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList) {
            return $this->tweetService->list($usersList);
        });
    }

    public function create(TweetRequest $tweetRequest): JsonResponse
    {
        return $this->handleServiceCall(function () use ($tweetRequest) {
            return $this->tweetService->create($tweetRequest);
        });
    }

    public function repost(Tweet $tweet): JsonResponse
    {
        return $this->handleServiceCall(function () use ($tweet) {
            return $this->tweetService->repost($tweet);
        });
    }

    public function thread(CreateThreadRequest $сreateThreadRequest): JsonResponse
    {
        return $this->handleServiceCall(function () use ($сreateThreadRequest) {
            return $this->tweetService->thread($сreateThreadRequest);
        });
    }

    public function unrepost(Tweet $tweet, Request $request): JsonResponse
    {
        return $this->handleServiceCall(function () use ($tweet, $request) {
            return $this->tweetService->unrepost($tweet, $request);
        });
    }

    public function delete(Tweet $tweet, Request $request): JsonResponse
    {
        return $this->handleServiceCall(function () use ($tweet, $request) {
            return $this->tweetService->delete($tweet, $request);
        });
    }
}
