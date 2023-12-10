<?php

namespace App\Modules\Tweet\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Tweet\Requests\CreateTweetDraftRequest;
use App\Modules\Tweet\Requests\RemoveTweetDraftsRequest;
use App\Modules\Tweet\Services\TweetDraftService;
use Illuminate\Http\JsonResponse;

class TweetDraftController extends Controller
{
    private $tweetDraftService;

    public function __construct(TweetDraftService $tweetDraftService)
    {
        $this->tweetDraftService = $tweetDraftService;
    }

    public function index(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->tweetDraftService->index();
        });
    }

    public function create(CreateTweetDraftRequest $request): JsonResponse
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->tweetDraftService->create($request);
        });
    }

    public function delete(RemoveTweetDraftsRequest $request): JsonResponse
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->tweetDraftService->delete($request);
        });
    }
}
