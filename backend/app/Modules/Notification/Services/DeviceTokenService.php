<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\DeviceToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Repositories\DeviceTokenRepository;
use App\Modules\Notification\Requests\DeviceTokenRequest;
use App\Modules\Notification\Requests\UpdateNotificationStatusRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DeviceTokenService
{
    private $deviceTokenRepository;
    private $authorizedUserId;

    public function __construct(
        DeviceTokenRepository $deviceTokenRepository
    ) {
        $this->deviceTokenRepository = $deviceTokenRepository;
        $this->authorizedUserId = Auth::id();
    }

    public function index()
    {
        return $this->deviceTokenRepository->getByUserId($this->authorizedUserId);
    }

    public function create(DeviceTokenRequest $request): void
    {
        $this->deviceTokenRepository->create($this->authorizedUserId, $request->token);
    }

    public function update(DeviceToken $deviceToken, DeviceTokenRequest $request): void
    {
        $this->deviceTokenRepository->update($deviceToken, $request->token);
    }

    public function delete(DeviceToken $deviceToken): void
    {
        $this->deviceTokenRepository->delete($deviceToken);
    }
}
