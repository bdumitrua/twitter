<?php

namespace App\Modules\Twitt\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Repositories\TwittFavoriteRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TwittFavoriteService
{
    private $twittFavoriteRepository;

    public function __construct(
        TwittFavoriteRepository $twittFavoriteRepository
    ) {
        $this->twittFavoriteRepository = $twittFavoriteRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return Cache::remember(KEY_USER_FAVORITES . $authorizedUserId, now()->addMinutes(5), function () use ($authorizedUserId) {
            return $this->twittFavoriteRepository->getByUserId($authorizedUserId);
        });
    }

    public function add(Twitt $twitt): void
    {
        $this->twittFavoriteRepository->add($twitt->id, Auth::id());
    }

    public function remove(Twitt $twitt): void
    {
        $this->twittFavoriteRepository->remove($twitt->id, Auth::id());
    }
}
