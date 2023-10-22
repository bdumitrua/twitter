<?php

namespace App\Modules\Twitt\Repositories;

use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Models\TwittFavorite;
use App\Modules\Twitt\Models\TwittLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TwittActionRepository
{
    protected $twittLike;
    protected $twittFavorite;

    public function __construct(
        TwittLike $twittLike,
        TwittFavorite $twittFavorite,
    ) {
        $this->twittLike = $twittLike;
        $this->twittFavorite = $twittFavorite;
    }
}
