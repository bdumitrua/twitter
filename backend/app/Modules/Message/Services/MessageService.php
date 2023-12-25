<?php

namespace App\Modules\Message\Services;

use App\Exceptions\UnprocessableContentException;
use App\Modules\Message\DTO\MessageDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Modules\Message\Repositories\MessageRepository;
use App\Modules\Message\Requests\MessageRequest;
use App\Modules\User\Models\User;
use App\Traits\CreateDTO;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    use CreateDTO;

    protected MessageRepository $messageRepository;
    protected LogManager $logger;
    protected ?int $authorizedUserId;

    public function __construct(
        MessageRepository $messageRepository,
        LogManager $logger,
    ) {
        $this->messageRepository = $messageRepository;
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    public function chats()
    {
        return $this->messageRepository->getUserChats($this->authorizedUserId);
    }

    public function index(User $user)
    {
        $participants = [
            $user->id,
            $this->authorizedUserId
        ];

        return $this->messageRepository->getChatMessages($participants);
    }

    public function send(User $user, MessageRequest $messageRequest)
    {
        $this->logger->info('Validating message data', $messageRequest->toArray());
        $this->validateMessageRequest($messageRequest);

        $participants = [
            $user->id,
            $this->authorizedUserId
        ];
        $chat = $this->messageRepository->findOrCreateChat($participants);

        $messageDTO = $this->createDTO($messageRequest, MessageDTO::class);
        $messageDTO->senderId = $this->authorizedUserId;
        $messageDTO->receiverId = $user->id;

        $this->logger->info('Sending message from messageDTO', $messageDTO->toArray());
        $this->messageRepository->send($messageDTO, $chat->id);
    }

    public function read(string $messageUuid): Response
    {
        return $this->messageRepository->read($messageUuid);
    }

    public function delete(string $messageUuid): Response
    {
        return $this->messageRepository->delete($messageUuid);
    }

    protected function validateMessageRequest(MessageRequest $messageRequest): void
    {
        if (empty($messageRequest->text) && empty($messageRequest->linkedEntityId)) {
            throw new UnprocessableContentException('Message can\'t be empty');
        }

        if (is_null($messageRequest->linkedEntityId) !== is_null($messageRequest->linkedEntityType)) {
            throw new UnprocessableContentException('Оба поля linkedEntityId и linkedEntityType должны быть заполнены или пустыми');
        }
    }
}
