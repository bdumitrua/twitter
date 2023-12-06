<?php

namespace App\Modules\Search\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Search\Models\Search;
use App\Modules\Search\Repositories\SearchRepository;
use App\Modules\Search\Requests\SearchRequest;
use Illuminate\Support\Facades\Auth;

class SearchService
{
    private SearchRepository $searchRepository;
    private int $authorizedUserId;

    public function __construct(
        SearchRepository $searchRepository
    ) {
        $this->searchRepository = $searchRepository;
        $this->authorizedUserId = Auth::id();
    }

    public function users(SearchRequest $request)
    {
        // 
    }

    public function tweets(SearchRequest $request)
    {
        // 
    }
}
