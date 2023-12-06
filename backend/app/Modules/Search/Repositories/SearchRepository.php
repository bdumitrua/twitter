<?php

namespace App\Modules\Search\Repositories;

use App\Modules\Search\Models\RecentSearch;
use App\Modules\Search\Models\Search;

class SearchRepository
{
    protected $recentSearch;

    public function __construct(RecentSearch $recentSearch)
    {
        $this->recentSearch = $recentSearch;
    }
}
