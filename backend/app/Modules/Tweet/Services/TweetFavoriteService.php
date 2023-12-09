<?php

namespace App\Modules\Tweet\Services;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Repositories\TweetFavoriteRepository;
use Illuminate\Database\Eloquent\Collection;
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

    public function index(): Collection
    {
        return $this->tweetFavoriteRepository->getByUserId($this->authorizedUserId);
    }

    public function add(Tweet $tweet): void
    {
        $this->tweetFavoriteRepository->add($tweet->id, $this->authorizedUserId);
    }

    public function remove(Tweet $tweet): void
    {
        $this->tweetFavoriteRepository->remove($tweet->id, $this->authorizedUserId);
    }
}
