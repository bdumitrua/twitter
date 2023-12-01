<?php

namespace App\Modules\User\Events;

use App\Modules\Tweet\Models\TweetNotice;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TweetNoticeEvent implements ShouldBroadcast
{
    public $tweetNotice;

    public function __construct(
        TweetNotice $tweetNotice,
    ) {
        $this->tweetNotice = $tweetNotice;
    }

    public function broadcastOn()
    {
        // You can implement your sockets logic here
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
    }
}
