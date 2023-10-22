<?php

namespace App\Modules\Twitt\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Requests\TwittRequest;
use App\Modules\Twitt\Services\TwittActionService;
use App\Modules\Twitt\Services\TwittService;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use Illuminate\Http\JsonResponse;

class TwittActionController extends Controller
{
    private $twittActionService;

    public function __construct(TwittActionService $twittActionService)
    {
        $this->twittActionService = $twittActionService;
    }

    public function index(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->twittActionService->index();
        });
    }

    public function show(Twitt $twitt): JsonResponse
    {
        return $this->handleServiceCall(function () use ($twitt) {
            return $this->twittActionService->show($twitt);
        });
    }
}
