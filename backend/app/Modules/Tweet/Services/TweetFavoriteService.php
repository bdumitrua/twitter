<?php

namespace App\Modules\Tweet\Services;

use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Repositories\TweetFavoriteRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TweetFavoriteService
{
    private $tweetFavoriteRepository;

    public function __construct(
        TweetFavoriteRepository $tweetFavoriteRepository
    ) {
        $this->tweetFavoriteRepository = $tweetFavoriteRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return Cache::remember(KEY_USER_FAVORITES . $authorizedUserId, TimeHelper::getMinutes(5), function () use ($authorizedUserId) {
            return $this->tweetFavoriteRepository->getByUserId($authorizedUserId);
        });
    }

    public function add(Tweet $tweet): void
    {
        $this->tweetFavoriteRepository->add($tweet->id, Auth::id());
    }

    public function remove(Tweet $tweet): void
    {
        $this->tweetFavoriteRepository->remove($tweet->id, Auth::id());
    }
}
