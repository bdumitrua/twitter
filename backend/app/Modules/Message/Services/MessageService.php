<?php

namespace App\Modules\Message\Services;

use App\Exceptions\UnprocessableContentException;
use App\Modules\Message\DTO\MessageDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Modules\Message\Repositories\MessageRepository;
use App\Modules\Message\Requests\MessageRequest;
use App\Modules\Message\Resources\ChatResource;
use App\Modules\Message\Resources\MessageResource;
use App\Modules\User\Models\User;
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
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

    /**
     * @return JsonResource
     */
    public function chats(): JsonResource
    {
        return ChatResource::collection(
            $this->messageRepository->getUserChats($this->authorizedUserId)
        );
    }

    /**
     * @param User $user
     * 
     * @return JsonResource
     */
    public function index(User $user): JsonResource
    {
        $participants = [
            $user->id,
            $this->authorizedUserId
        ];

        $chat = $this->messageRepository->findOrCreateChat($participants);

        return MessageResource::collection(
            $this->messageRepository->getChatMessages($chat)
        );
    }

    /**
     * @param User $user
     * @param MessageRequest $messageRequest
     * 
     * @return void
     */
    public function send(User $user, MessageRequest $messageRequest): void
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

        $this->logger->info('Sending message from messageDTO', $messageDTO->toArray());
        $this->messageRepository->send($messageDTO, $chat->id);
    }

    /**
     * @param string $messageUuid
     * 
     * @return Response
     */
    public function read(string $messageUuid): Response
    {
        return $this->messageRepository->read($messageUuid);
    }

    /**
     * @param string $messageUuid
     * 
     * @return Response
     */
    public function delete(string $messageUuid): Response
    {
        $this->logger->info('Deleting message', ['messageUuid' => $messageUuid]);
        return $this->messageRepository->delete($messageUuid);
    }

    /**
     * @param MessageRequest $messageRequest
     * 
     * @return void
     */
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
