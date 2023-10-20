<?php

namespace App\Modules\Twitt\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Services\TwittService;

class TwittController extends Controller
{
    private $twittService;

    public function __construct(TwittService $twittService)
    {
        $this->twittService = $twittService;
    }

    // Method realization example
    public function show(Twitt $twitt)
    {
        return $this->handleServiceCall(function () use ($twitt) {
            return $this->twittService->show($twitt);
        });
    }

}
