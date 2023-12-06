<?php

namespace App\Modules\Search\Services;

use App\Modules\Search\DTO\RecentSearchDTO;
use App\Modules\Search\Models\RecentSearch;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Search\Models\Search;
use App\Modules\Search\Repositories\RecentSearchRepository;
use App\Modules\Search\Repositories\SearchRepository;
use App\Modules\Search\Requests\SearchRequest;
use App\Modules\Search\Resources\RecentSearchResource;
use App\Modules\Tweet\Repositories\TweetRepository;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class SearchService
{
    private RecentSearchRepository $recentSearchRepository;
    private TweetRepository $tweetRepository;
    private UserRepository $userRepository;
    private ?int $authorizedUserId;

    public function __construct(
        RecentSearchRepository $recentSearchRepository,
        TweetRepository $tweetRepository,
        UserRepository $userRepository,
    ) {
        $this->recentSearchRepository = $recentSearchRepository;
        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;
        $this->authorizedUserId = Auth::id();
    }

    public function index()
    {
        // return RecentSearchResource::collection(
        //     $this->recentSearchRepository->getByUserId($this->authorizedUserId)
        // );

        return $this->recentSearchRepository->getByUserId($this->authorizedUserId);
    }

    public function users(SearchRequest $request)
    {
        return $this->userRepository->search($request->search);
    }

    public function tweets(SearchRequest $request)
    {
        // return $this->tweetRepository->search($request->search);
    }
}
