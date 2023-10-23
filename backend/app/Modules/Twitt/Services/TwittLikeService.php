<?php

namespace App\Modules\Twitt\Services;

use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Repositories\TwittLikeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TwittLikeService
{
    private $twittLikeRepository;

    public function __construct(
        TwittLikeRepository $twittLikeRepository
    ) {
        $this->twittLikeRepository = $twittLikeRepository;
    }

    public function index(): Collection
    {
        $authorizedUserId = Auth::id();
        return Cache::remember(KEY_USER_LIKES . $authorizedUserId, TimeHelper::getMinutes(5), function () use ($authorizedUserId) {
            return $this->twittLikeRepository->getByUserId($authorizedUserId);
        });
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
