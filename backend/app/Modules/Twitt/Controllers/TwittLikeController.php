<?php

namespace App\Modules\Twitt\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Requests\TwittRequest;
use App\Modules\Twitt\Services\TwittLikeService;
use App\Modules\Twitt\Services\TwittService;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use Illuminate\Http\JsonResponse;

class TwittLikeController extends Controller
{
    private $twittLikeService;

    public function __construct(TwittLikeService $twittLikeService)
    {
        $this->twittLikeService = $twittLikeService;
    }

    public function index(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->twittLikeService->index();
        });
    }

    public function show(Twitt $twitt): JsonResponse
    {
        return $this->handleServiceCall(function () use ($twitt) {
            return $this->twittLikeService->show($twitt);
        });
    }
}
