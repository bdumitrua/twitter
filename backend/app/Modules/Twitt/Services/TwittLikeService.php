<?php

namespace App\Modules\Twitt\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Repositories\TwittLikeRepository;
use App\Traits\GetCachedData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TwittLikeService
{
    use GetCachedData;

    private $twittLikeRepository;

    public function __construct(
        TwittLikeRepository $twittLikeRepository
    ) {
        $this->twittLikeRepository = $twittLikeRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return $this->getCachedData('user_likes:' . $authorizedUserId, function () use ($authorizedUserId) {
            return $this->twittLikeRepository->getByUserId($authorizedUserId);
        }, 300);
    }

    public function add(Twitt $twitt): void
    {
        $this->twittLikeRepository->add($twitt->id, Auth::id());
    }

    public function remove(Twitt $twitt): void
    {
        $this->twittLikeRepository->remove($twitt->id, Auth::id());
    }
}
