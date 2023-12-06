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
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

class DeviceTokenService
{
    private DeviceTokenRepository $deviceTokenRepository;
    protected LogManager $logger;
    private ?int $authorizedUserId;

    public function __construct(
        DeviceTokenRepository $deviceTokenRepository,
        LogManager $logger,
    ) {
        $this->deviceTokenRepository = $deviceTokenRepository;
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    public function index()
    {
        return $this->deviceTokenRepository->getByUserId($this->authorizedUserId);
    }

    public function create(DeviceTokenRequest $request): void
    {
        $this->logger->info('Creating DeviceToken from create request', $request->toArray());
        $this->deviceTokenRepository->create($this->authorizedUserId, $request->token);
    }

    public function update(DeviceToken $deviceToken, DeviceTokenRequest $request): void
    {
        $this->logger->info(
            'Updating DeviceToken from update request',
            [
                'Current deviceToken' => $deviceToken->toArray(),
                'Request' => array_merge($request->toArray(), ['ip' => $request->ip()])
            ]
        );
        $this->deviceTokenRepository->update($deviceToken, $request->token);
    }

    public function delete(DeviceToken $deviceToken, Request $request): void
    {
        $this->logger->info('Deleting deviceToken', [$deviceToken->toArray(), 'ip' => $request->ip()]);
        $this->deviceTokenRepository->delete($deviceToken);
    }
}
