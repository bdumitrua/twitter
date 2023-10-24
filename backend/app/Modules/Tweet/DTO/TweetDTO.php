<?php

namespace App\Modules\Tweet\DTO;

class TweetDTO
{
    public ?string $text = null;
    public ?int $userGroupId = null;
    public ?bool $isComment = false;
    public ?int $commentedTweetId = null;
    public ?bool $isReply = false;
    public ?int $repliedTweetId = null;
    public ?bool $isRepost = false;
    public ?int $repostedTweetId = null;
}
