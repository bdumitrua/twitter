<?php

namespace App\Modules\User\Events;

use App\Modules\Tweet\Models\TweetNotice;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TweetNoticeEvent
{
    public $tweetNotice;

    public function __construct(
        TweetNotice $tweetNotice,
    ) {
        $this->tweetNotice = $tweetNotice;
    }
}
