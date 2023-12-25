<?php

namespace App\Modules\Message\Repositories;

use App\Helpers\ResponseHelper;
use App\Modules\Message\DTO\MessageDTO;
use App\Modules\Message\Models\Chat;
use App\Modules\Message\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class MessageRepository
{
    protected Chat $chat;

    public function __construct(
        Chat $chat
    ) {
        $this->chat = $chat;
    }

    protected function queryUserChats(int $userId): Builder
    {
        return $this->chat
            ->whereIn('first_user_id', $userId)
            ->orWhereIn('second_user_id', $userId)
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

    public function getChatMessages(array $participants): array
    {
        return [];
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

    public function send(MessageDTO $messageDTO): void
    {
        // 
    }

    public function read(int $messageId): Response
    {
        return ResponseHelper::okResponse();
    }

    public function delete(int $messageId): Response
    {
        return ResponseHelper::okResponse();
    }
}
