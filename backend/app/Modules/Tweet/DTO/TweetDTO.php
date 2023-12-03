<?php

namespace App\Modules\Tweet\DTO;

class TweetDTO
{
    public ?int $userId = null;
    public ?string $text = null;
    public ?int $userGroupId = null;
    public ?string $type = null;
    public ?int $linkedTweetId = null;

    public function __construct(int $userId, array $data)
    {
        $this->userId = $userId;
        $this->text = $data['text'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->userGroupId = $data['userGroupId'] ?? null;
        $this->linkedTweetId = $data['linkedTweetId'] ?? null;
    }

    public function toArray()
    {
        return [
            'user_id' => $this->userId,
            'text' => $this->text,
            'user_group_id' => $this->userGroupId,
            'type' => $this->type,
            'linked_tweet_id' => $this->linkedTweetId,
        ];
    }
}
