<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Services\UserSubscriptionService;

class UserSubscribtionController extends Controller
{
    private $userSubscriptionService;

    public function __construct(UserSubscriptionService $userSubscriptionService)
    {
        $this->userSubscriptionService = $userSubscriptionService;
    }

    // public function index()
    // {
    //     return $this->handleServiceCall(function () {
    //         return $this->userSubscriptionService->index();
    //     });
    // }
}
