<?php

namespace App\Modules\Twitt\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Services\TwittLikeService;
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

    public function add(Twitt $twitt): JsonResponse
    {
        return $this->handleServiceCall(function () use ($twitt) {
            return $this->twittLikeService->add($twitt);
        });
    }

    public function remove(Twitt $twitt): JsonResponse
    {
        return $this->handleServiceCall(function () use ($twitt) {
            return $this->twittLikeService->remove($twitt);
        });
    }
}
