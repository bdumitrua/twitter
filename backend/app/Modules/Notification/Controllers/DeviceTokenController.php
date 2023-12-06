<?php

namespace App\Modules\Notification\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Notification\Requests\DeviceTokenRequest;
use App\Modules\Notification\Services\DeviceTokenService;

class DeviceTokenController extends Controller
{
    private $deviceTokenService;

    public function __construct(DeviceTokenService $deviceTokenService)
    {
        $this->deviceTokenService = $deviceTokenService;
    }

    public function index()
    {
        return $this->handleServiceCall(function () {
            return $this->deviceTokenService->index();
        });
    }

    public function create(DeviceTokenRequest $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->deviceTokenService->create($request);
        });
    }


    public function update(DeviceToken $deviceToken, DeviceTokenRequest $request)
    {
        return $this->handleServiceCall(function () use ($deviceToken, $request) {
            return $this->deviceTokenService->update($deviceToken, $request);
        });
    }


    public function delete(DeviceToken $deviceToken, Request $request)
    {
        return $this->handleServiceCall(function () use ($deviceToken, $request) {
            return $this->deviceTokenService->delete($deviceToken, $request);
        });
    }
}
