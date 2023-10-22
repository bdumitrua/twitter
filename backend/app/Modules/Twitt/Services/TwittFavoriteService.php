<?php

namespace App\Modules\Twitt\Services;

use App\Modules\Twitt\DTO\TwittDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Repositories\TwittFavoriteRepository;
use App\Modules\Twitt\Requests\TwittRequest;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

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
        return $this->twittFavoriteRepository->getByUserId(Auth::id());
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