<?php

namespace App\Modules\Tweet\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Requests\CreateThreadRequest;
use App\Modules\Tweet\Requests\TweetRequest;
use App\Modules\Tweet\Services\TweetDraftService;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
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

    public function create(Request $request): JsonResponse
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->tweetDraftService->create($request);
        });
    }

    public function delete(Request $request): JsonResponse
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->tweetDraftService->delete($request);
        });
    }
}
