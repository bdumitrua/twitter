<?php

namespace App\Modules\Message\Repositories;

use App\Firebase\FirebaseService;
use App\Helpers\ResponseHelper;
use App\Modules\Message\DTO\MessageDTO;
use App\Modules\Message\Models\Chat;
use App\Modules\Message\Models\ChatMessage;
use App\Modules\Message\Models\Message;
use App\Modules\Tweet\Repositories\TweetRepository;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MessageRepository
{
    protected Chat $chat;
    protected ChatMessage $chatMessage;
    protected UserRepository $userRepository;
    protected FirebaseService $firebaseService;
    protected TweetRepository $tweetRepository;

    public function __construct(
        Chat $chat,
        ChatMessage $chatMessage,
        UserRepository $userRepository,
        FirebaseService $firebaseService,
        TweetRepository $tweetRepository,
    ) {
        $this->chat = $chat;
        $this->chatMessage = $chatMessage;
        $this->userRepository = $userRepository;
        $this->firebaseService = $firebaseService;
        $this->tweetRepository = $tweetRepository;
    }

    /**
     * @param array $participants
     * 
     * @return Builder
     */
    protected function queryChatByParticipants(array $participants): Builder
    {
        return $this->chat
            ->whereIn('first_user_id', $participants)
            ->whereIn('second_user_id', $participants);
    }

    /**
     * @param array $participants
     * 
     * @return Chat
     */
    public function findOrCreateChat(array $participants): Chat
    {
        $chat = $this->queryChatByParticipants($participants)->first();

        if (empty($chat)) {
            $chat = $this->create($participants);
        }

        return $chat;
    }

    /**
     * @param Chat $chat
     * 
     * @return array
     */
    public function getChatMessages(Chat $chat): array
    {
        $messages = $this->firebaseService->getChatMessages($chat->id);

        foreach ($messages as &$message) {
            if (isset(
                $message['linkedEntityId'],
                $message['linkedEntityType']
            )) {
                $message['linkedEntityData'] = $this->getLinkedEntityData(
                    $message['linkedEntityId'],
                    $message['linkedEntityType'],
                );
            }
        }

        return $messages;
    }

    /**
     * @param int $userId
     * 
     * @return Collection
     */
    public function getUserChats(int $userId): Collection
    {
        $chats = $this->chat
            ->where('first_user_id', $userId)
            ->orWhere('second_user_id', $userId)
            ->latest('updated_at')
            ->get();

        foreach ($chats as &$chat) {
            $this->assembleChatData($chat);
        }

        return $chats;
    }

    /**
     * @param array $participants
     * 
     * @return Chat
     */
    public function create(array $participants): Chat
    {
        $data = [
            'first_user_id' => $participants[0],
            'second_user_id' => $participants[1],
        ];

        return $this->chat->create($data);
    }

    /**
     * @param MessageDTO $messageDTO
     * @param int $chatId
     * 
     * @return void
     */
    public function send(MessageDTO $messageDTO, int $chatId): void
    {
        $messageUuid = $this->firebaseService->sendMessage($messageDTO, $chatId);

        $this->chatMessage->create([
            'chat_id' => $chatId,
            'message_uuid' => $messageUuid
        ]);

        $this->updateChatTimestamp($chatId);
    }

    /**
     * @param string $messageUuid
     * 
     * @return Response
     */
    public function read(string $messageUuid): Response
    {
        $chatId = $this->getChatIdByMessage($messageUuid);
        $messageStatusUpdated = $this->firebaseService->readMessage($messageUuid, $chatId);

        return ResponseHelper::okResponse($messageStatusUpdated);
    }

    /**
     * @param string $messageUuid
     * 
     * @return Response
     */
    public function delete(string $messageUuid): Response
    {
        $chatId = $this->getChatIdByMessage($messageUuid);
        $lastChatActivityTime = $this->firebaseService->deleteMessage($messageUuid, $chatId);

        if (empty($lastChatActivityTime)) {
            return ResponseHelper::noContent();
        }

        $this->chatMessage->where('message_uuid', $messageUuid)->delete();
        $this->updateChatTimestamp($chatId, date('Y-m-d H:i:s', $lastChatActivityTime / 1000));

        return ResponseHelper::okResponse();
    }

    /**
     * @param string $messageUuid
     * 
     * @return int
     */
    protected function getChatIdByMessage(string $messageUuid): int
    {
        $chatMessage = $this->chatMessage->where('message_uuid', $messageUuid)->first();

        if (empty($chatMessage)) {
            return 0;
        }

        return $chatMessage->pluck('chat_id')->toArray()[0];
    }

    /**
     * @param int $chatId
     * @param mixed $date
     * 
     * @return void
     */
    protected function updateChatTimestamp(int $chatId, $date = null): void
    {
        $date = $date ?? now();
        $this->chat->find($chatId)->update([
            'updated_at' => $date
        ]);
    }

    /**
     * @param int $linkedEntityId
     * @param string $linkedEntityType
     * 
     * @return Model
     */
    protected function getLinkedEntityData(int $linkedEntityId, string $linkedEntityType): Model
    {
        if ($linkedEntityType === 'tweet') {
            return $this->tweetRepository->getTweetData($linkedEntityId);
        }
    }

    /**
     * @param Chat $chat
     * 
     * @return void
     */
    protected function assembleChatData(Chat &$chat): void
    {
        $interlocutorId = $chat->first_user_id === Auth::id() ? $chat->second_user_id : $chat->first_user_id;

        $chat->interlocutorId = $interlocutorId;
        $chat->interlocutorData = $this->userRepository->getUserData($interlocutorId);
    }
}
