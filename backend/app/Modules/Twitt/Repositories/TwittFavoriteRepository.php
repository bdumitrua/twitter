<?php

namespace App\Modules\Twitt\Repositories;

use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Models\TwittFavorite;
use App\Modules\Twitt\Models\TwittLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TwittFavoriteRepository
{
    protected $twittFavorite;

    public function __construct(
        TwittFavorite $twittFavorite,
    ) {
        $this->twittFavorite = $twittFavorite;
    }
}
