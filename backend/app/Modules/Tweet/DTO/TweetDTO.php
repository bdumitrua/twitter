<?php

namespace App\Modules\Tweet\DTO;

class TweetDTO
{
    public ?string $text = null;
    public ?int $userGroupId = null;
    public ?string $type = null;
    public ?int $linkedTweetId = null;

    public function toArray()
    {
        return [
            'text' => $this->text,
            'user_group_id' => $this->userGroupId,
            'type' => $this->type,
            'linked_tweet_id' => $this->linkedTweetId,
        ];
    }
}
