<?php

namespace App\Modules\Message\Repositories;

use App\Firebase\FirebaseService;
use App\Helpers\ResponseHelper;
use App\Modules\Message\DTO\MessageDTO;
use App\Modules\Message\Models\Chat;
use App\Modules\Message\Models\ChatMessage;
use App\Modules\Message\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class MessageRepository
{
    protected Chat $chat;
    protected ChatMessage $chatMessage;
    protected FirebaseService $firebaseService;

    public function __construct(
        Chat $chat,
        ChatMessage $chatMessage,
        FirebaseService $firebaseService
    ) {
        $this->chat = $chat;
        $this->chatMessage = $chatMessage;
        $this->firebaseService = $firebaseService;
    }

    protected function queryUserChats(int $userId): Builder
    {
        return $this->chat
            ->where('first_user_id', $userId)
            ->orWhere('second_user_id', $userId)
            ->latest('updated_at');
    }

    protected function queryChatByParticipants(array $participants): Builder
    {
        return $this->chat
            ->whereIn('first_user_id', $participants)
            ->whereIn('second_user_id', $participants);
    }

    public function findOrCreateChat(array $participants): Chat
    {
        $chat = $this->queryChatByParticipants($participants)->first();

        if (empty($chat)) {
            $chat = $this->create($participants);
        }

        return $chat;
    }

    public function getChatMessages(Chat $chat): array
    {
        return $this->firebaseService->getChatMessages($chat->id);
    }

    public function getUserChats(int $userId): Collection
    {
        $chats = $this->queryUserChats($userId)->get();

        return $chats;
    }

    public function create(array $participants): Chat
    {
        $data = [
            'first_user_id' => $participants[0],
            'second_user_id' => $participants[1],
        ];

        return $this->chat->create($data);
    }

    public function send(MessageDTO $messageDTO, int $chatId): void
    {
        $messageUuid = $this->firebaseService->sendMessage($messageDTO, $chatId);

        $this->chatMessage->create([
            'chat_id' => $chatId,
            'message_uuid' => $messageUuid
        ]);

        $this->updateChatTimestamp($chatId);
    }

    public function read(string $messageUuid): Response
    {
        $chatId = $this->getChatIdByMessage($messageUuid);
        $messageStatusUpdated = $this->firebaseService->readMessage($messageUuid, $chatId);

        return ResponseHelper::okResponse($messageStatusUpdated);
    }

    public function delete(string $messageUuid): Response
    {
        $chatId = $this->getChatIdByMessage($messageUuid);
        $lastChatActivityTime = $this->firebaseService->deleteMessage($messageUuid, $chatId);

        if (empty($lastChatActivityTime)) {
            return ResponseHelper::noContent();
        }

        $this->chatMessage->where('message_uuid', $messageUuid)->delete();
        $this->updateChatTimestamp($chatId, date('Y-m-d H:i:s', $lastChatActivityTime));

        return ResponseHelper::okResponse();
    }

    protected function getChatIdByMessage(string $messageUuid): int
    {
        $chatMessage = $this->chatMessage->where('message_uuid', $messageUuid)->first();

        if (empty($chatMessage)) {
            return 0;
        }

        return $chatMessage->pluck('chat_id')->toArray()[0];
    }

    protected function updateChatTimestamp(int $chatId, $date = null): void
    {
        $date = $date ?? now();
        $this->chat->find($chatId)->update([
            'updated_at' => $date
        ]);
    }
}
