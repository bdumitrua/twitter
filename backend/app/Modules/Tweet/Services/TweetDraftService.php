<?php

namespace App\Modules\Tweet\Services;

use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Repositories\TweetDraftRepository;
use App\Modules\Tweet\Requests\CreateTweetDraftRequest;
use App\Modules\Tweet\Requests\RemoveTweetDraftsRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

    public function index(): Collection
    {
        return $this->tweetDraftRepository->getByUserId($this->authorizedUserId);
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
