<?php

namespace App\Modules\Tweet\DTO;

class TweetDTO
{
    public ?string $text = null;
    public ?int $userGroupId = null;
    public ?string $type = 'default';
    public ?int $linkedTweetId = null;
}
