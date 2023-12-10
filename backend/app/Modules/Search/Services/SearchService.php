<?php

namespace App\Modules\Search\Services;

use App\Modules\Search\DTO\RecentSearchDTO;
use App\Modules\Search\Repositories\RecentSearchRepository;
use App\Modules\Search\Requests\RecentSearchRequest;
use App\Modules\Search\Requests\SearchRequest;
use App\Modules\Search\Resources\RecentSearchesResource;
use App\Modules\Tweet\Repositories\TweetRepository;
use App\Modules\Tweet\Resources\TweetResource;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

class SearchService
{
    protected RecentSearchRepository $recentSearchRepository;
    protected TweetRepository $tweetRepository;
    protected UserRepository $userRepository;
    protected LogManager $logger;
    protected ?int $authorizedUserId;

    public function __construct(
        RecentSearchRepository $recentSearchRepository,
        TweetRepository $tweetRepository,
        UserRepository $userRepository,
        LogManager $logger,
    ) {
        $this->recentSearchRepository = $recentSearchRepository;
        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    public function index()
    {
        return new RecentSearchesResource(
            $this->recentSearchRepository->getByUserId($this->authorizedUserId)
        );
    }

    public function users(SearchRequest $request)
    {
        return ShortUserResource::collection(
            $this->userRepository->search($request->search)
        );
    }

    public function tweets(SearchRequest $request)
    {
        return TweetResource::collection(
            $this->tweetRepository->search($request->search)
        );
    }

    public function create(RecentSearchRequest $request)
    {
        $this->logger->info(
            'Creating recentSearchDTO from create request',
            array_merge($request->toArray(), ['userId' => $this->authorizedUserId])
        );

        $recentSearchDTO = new RecentSearchDTO();
        $recentSearchDTO->text = $request->text;
        $recentSearchDTO->userId = $this->authorizedUserId;
        $recentSearchDTO->linkedUserId = $request->linkedUserId;

        $this->logger->info(
            'Creating user RecentSearch from recentSearchDTO',
            $recentSearchDTO->toArray()
        );
        $this->recentSearchRepository->create($recentSearchDTO);
    }

    public function clear()
    {
        $this->logger->info(
            'Clearing user RecentSearch',
            ['userId' => $this->authorizedUserId]
        );
        $this->recentSearchRepository->clear($this->authorizedUserId);
    }
}
