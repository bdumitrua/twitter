<?php

namespace App\Modules\Twitt\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Requests\TwittRequest;
use App\Modules\Twitt\Services\TwittFavoriteService;
use App\Modules\Twitt\Services\TwittService;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use Illuminate\Http\JsonResponse;

class TwittFavoriteController extends Controller
{
    private $twittFavoriteService;

    public function __construct(TwittFavoriteService $twittFavoriteService)
    {
        $this->twittFavoriteService = $twittFavoriteService;
    }

    public function index(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->twittFavoriteService->index();
        });
    }

    public function show(Twitt $twitt): JsonResponse
    {
        return $this->handleServiceCall(function () use ($twitt) {
            return $this->twittFavoriteService->show($twitt);
        });
    }
}
