<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Modules\Notification\Repositories\DeviceTokenRepository;
use App\Modules\Notification\Requests\DeviceTokenRequest;
use App\Modules\Notification\Resources\DeviceTokenResource;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

class NotificationsSubscribtionService
{
    protected LogManager $logger;
    protected ?int $authorizedUserId;

    public function __construct(
        LogManager $logger,
    ) {
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    public function subscribe(User $user)
    {
        // 
    }

    public function unsubscribe(User $user)
    {
        // 
    }
}
