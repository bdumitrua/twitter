<?php

namespace App\Modules\Message\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Message\Models\Message;
use App\Modules\Message\Requests\MessageRequest;
use App\Modules\Message\Services\MessageService;
use App\Modules\User\Models\User;

class MessageController extends Controller
{
    private $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->messageService->index($user);
        });
    }

    public function send(User $user, MessageRequest $messageRequest)
    {
        return $this->handleServiceCall(function () use ($user, $messageRequest) {
            return $this->messageService->send($user, $messageRequest);
        });
    }

    public function read(int $messageId)
    {
        return $this->handleServiceCall(function () use ($messageId) {
            return $this->messageService->read($messageId);
        });
    }

    public function delete(int $messageId)
    {
        return $this->handleServiceCall(function () use ($messageId) {
            return $this->messageService->delete($messageId);
        });
    }

    public function chats()
    {
        return $this->handleServiceCall(function () {
            return $this->messageService->chats();
        });
    }
}
