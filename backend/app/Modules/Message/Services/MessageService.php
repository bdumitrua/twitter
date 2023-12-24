<?php

namespace App\Modules\Message\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Modules\Message\Repositories\MessageRepository;
use App\Modules\Message\Requests\MessageRequest;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    protected MessageRepository $messageRepository;
    protected ?int $authorizedUserId;


    public function __construct(
        MessageRepository $messageRepository
    ) {
        $this->messageRepository = $messageRepository;
        $this->authorizedUserId = Auth::id();
    }

    public function index(User $user)
    {
        // 
    }

    public function send(User $user, MessageRequest $messageRequest)
    {
        //
    }

    public function read(int $messageId)
    {
        // 
    }

    public function delete(int $messageId)
    {
        // 
    }

    public function chats()
    {
        // 
    }
}
