<?php

namespace App\Modules\Tweet\Services;

use App\Modules\Tweet\Repositories\TweetDraftRepository;
use App\Modules\Tweet\Requests\CreateTweetDraftRequest;
use App\Modules\Tweet\Requests\RemoveTweetDraftsRequest;
use App\Modules\Tweet\Resources\TweetDraftResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TweetDraftService
{
    private TweetDraftRepository $tweetDraftRepository;
    protected ?int $authorizedUserId;

    public function __construct(
        TweetDraftRepository $tweetDraftRepository
    ) {
        $this->tweetDraftRepository = $tweetDraftRepository;
        $this->authorizedUserId = Auth::id();
    }

    public function index(): JsonResource
    {
        return TweetDraftResource::collection(
            $this->tweetDraftRepository->getByUserId($this->authorizedUserId)
        );
    }

    public function create(CreateTweetDraftRequest $request): void
    {
        $this->tweetDraftRepository->create($request->text, $this->authorizedUserId);
    }

    public function delete(RemoveTweetDraftsRequest $request): void
    {
        $this->tweetDraftRepository->delete($request->drafts, $this->authorizedUserId);
    }
}
