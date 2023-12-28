<?php

namespace App\Modules\Message\Repositories;

use App\Exceptions\UnprocessableContentException;
use App\Firebase\FirebaseService;
use App\Helpers\ResponseHelper;
use App\Modules\Message\DTO\MessageDTO;
use App\Modules\Message\Models\Chat;
use App\Modules\Message\Models\ChatMessage;
use App\Modules\Message\Models\HiddenChat;
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
    protected HiddenChat $hiddenChat;
    protected ChatMessage $chatMessage;
    protected UserRepository $userRepository;
    protected FirebaseService $firebaseService;
    protected TweetRepository $tweetRepository;

    public function __construct(
        Chat $chat,
        HiddenChat $hiddenChat,
        ChatMessage $chatMessage,
        UserRepository $userRepository,
        FirebaseService $firebaseService,
        TweetRepository $tweetRepository,
    ) {
        $this->chat = $chat;
        $this->hiddenChat = $hiddenChat;
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
     * @param int $authorizedUserId
     * 
     * @return array
     */
    public function getChatMessages(Chat $chat, int $authorizedUserId): array
    {
        $messages = $this->firebaseService->getChatMessages($chat->id, $authorizedUserId) ?? [];
        $messagesFinal = [];

        foreach ($messages as $uuid => $message) {
            $message['uuid'] = $uuid;

            if (isset(
                $message['linkedEntityId'],
                $message['linkedEntityType']
            )) {
                $message['linkedEntityData'] = $this->getLinkedEntityData(
                    $message['linkedEntityId'],
                    $message['linkedEntityType'],
                );
            }

            $messagesFinal[] = $message;
        }

        return $messagesFinal;
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

        $hiddenChatsIds = $this->hiddenChat
            ->where('user_id', $userId)
            ->get()
            ->pluck('chat_id')
            ->toArray();

        $chats = $chats->filter(function ($chat) use ($hiddenChatsIds) {
            return !in_array($chat->id, $hiddenChatsIds);
        });

        foreach ($chats as &$chat) {
            $this->assembleChatData($chat);
        }

        return $chats;
    }

    /**
     * @param array $participants
     * 
     * @return Chat
     * 
     * @throws UnprocessableContentException
     */
    public function create(array $participants): Chat
    {
        if ($participants[0] === $participants[1]) {
            throw new UnprocessableContentException('You can\'t write to yourself');
        }

        $data = [
            'first_user_id' => $participants[0],
            'second_user_id' => $participants[1],
        ];

        return $this->chat->create($data);
    }

    /**
     * @param int $chatId
     * @param int $authorizedUserId
     * 
     * @return Response
     */
    public function clear(int $chatId, int $authorizedUserId): Response
    {
        $chatMessagesDeleted = $this->firebaseService->clearChatMessages($chatId, $authorizedUserId);
        $this->hideChatFromUser($chatId, $authorizedUserId);

        return ResponseHelper::okResponse($chatMessagesDeleted);
    }

    /**
     * @param MessageDTO $messageDTO
     * @param int $chatId
     * @param array $participants
     * 
     * @return void
     */
    public function send(MessageDTO $messageDTO, int $chatId, array $participants): void
    {
        $messageUuid = $this->firebaseService->sendMessage($messageDTO, $chatId, $participants);

        $this->chatMessage->create([
            'chat_id' => $chatId,
            'message_uuid' => $messageUuid
        ]);
        $this->hiddenChat->where('chat_id', $chatId)->delete();
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
        if (empty($chatId)) {
            return ResponseHelper::noContent();
        }

        $messageStatusUpdated = $this->firebaseService->readMessage($messageUuid, $chatId);
        return ResponseHelper::okResponse($messageStatusUpdated);
    }

    /**
     * @param string $messageUuid
     * @param int $authorizedUserId
     * 
     * @return Response
     */
    public function delete(string $messageUuid, int $authorizedUserId): Response
    {
        $chatId = $this->getChatIdByMessage($messageUuid);
        if (empty($chatId)) {
            return ResponseHelper::noContent();
        }

        $lastChatActivityTime = $this->firebaseService->deleteMessage($messageUuid, $chatId, $authorizedUserId);
        if ($lastChatActivityTime === null) {
            return ResponseHelper::noContent();
        }
        if ($lastChatActivityTime === 0) {
            $this->hideChatFromUser($chatId, $authorizedUserId);
        }

        return ResponseHelper::okResponse();
    }

    /**
     * @param string $messageUuid
     * 
     * @return int
     */
    protected function getChatIdByMessage(string $messageUuid): int
    {
        $chatMessage = $this->chatMessage->where('message_uuid', $messageUuid)->first(['chat_id']);
        if (empty($chatMessage)) {
            return 0;
        }

        return $chatMessage->toArray()['chat_id'];
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
        $chat = $this->chat->find($chatId)->first();

        $chat->updated_at = $date;
        $chat->save();
    }

    /**
     * @param int $chatId
     * @param int $userId
     * 
     * @return void
     */
    protected function hideChatFromUser(int $chatId, int $userId): void
    {
        $this->hiddenChat->create([
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }

    /**
     * @param int $linkedEntityId
     * @param string $linkedEntityType
     * 
     * @return ?Model
     */
    protected function getLinkedEntityData(int $linkedEntityId, string $linkedEntityType): ?Model
    {
        if ($linkedEntityType === 'tweet') {
            return $this->tweetRepository->getTweetData($linkedEntityId);
        }

        return null;
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
