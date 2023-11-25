<?php

namespace App\Modules\Notification\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Notification\Services\DeviceTokenService;

class DeviceTokenController extends Controller
{
    private $deviceTokenService;

    public function __construct(DeviceTokenService $deviceTokenService)
    {
        $this->deviceTokenService = $deviceTokenService;
    }
}
