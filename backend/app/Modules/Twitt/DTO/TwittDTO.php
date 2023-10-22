<?php

namespace App\Modules\Twitt\DTO;

use App\Modules\Base\BaseDTO;

class TwittDTO extends BaseDTO
{
    public ?string $text = null;
    public ?int $userGroupId = null;
    public ?bool $isComment = false;
    public ?int $commentedTwittId = null;
    public ?bool $isReply = false;
    public ?int $repliedTwittId = null;
    public ?bool $isRepost = false;
    public ?int $repostedTwittId = null;
}
