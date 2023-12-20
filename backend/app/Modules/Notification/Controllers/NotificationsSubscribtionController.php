<?php

namespace App\Modules\Notification\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Notification\Services\NotificationsSubscribtionService;
use App\Modules\User\Models\User;

class NotificationsSubscribtionController extends Controller
{
    private $notificationsSubscribtionService;

    public function __construct(
        NotificationsSubscribtionService $notificationsSubscribtionService
    ) {
        $this->notificationsSubscribtionService = $notificationsSubscribtionService;
    }

    public function subscribe(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->notificationsSubscribtionService->subscribe($user);
        });
    }

    public function unsubscribe(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->notificationsSubscribtionService->unsubscribe($user);
        });
    }
}
