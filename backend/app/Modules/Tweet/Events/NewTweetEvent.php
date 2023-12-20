<?php

namespace App\Modules\Tweet\Events;

use App\Modules\Tweet\Models\Tweet;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewTweetEvent
{
    public $tweet;

    public function __construct(
        Tweet $tweet,
    ) {
        $this->tweet = $tweet;
    }
}
