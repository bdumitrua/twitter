<?php

namespace App\Modules\Search\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Search\Requests\RecentSearchRequest;
use App\Modules\Search\Requests\SearchRequest;
use App\Modules\Search\Services\SearchService;

class SearchController extends Controller
{
    private $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index()
    {
        return $this->handleServiceCall(function () {
            return $this->searchService->index();
        });
    }

    public function users(SearchRequest $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->searchService->users($request);
        });
    }

    public function tweets(SearchRequest $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->searchService->tweets($request);
        });
    }

    public function create(RecentSearchRequest $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->searchService->create($request);
        });
    }

    public function clear()
    {
        return $this->handleServiceCall(function () {
            return $this->searchService->clear();
        });
    }
}
