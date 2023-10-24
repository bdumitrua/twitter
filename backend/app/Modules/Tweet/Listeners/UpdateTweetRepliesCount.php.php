<?php

namespace App\Modules\User\Listeners;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UserSubscribtion;

class UpdateTweetRepliesCount
{
    public function handle($event)
    {
        /** @var Tweet */
        $tweetId = $event->tweetId;
        $add = $event->add;

        $tweet = Tweet::find($tweetId);
        if (!empty($add)) {
            $tweet->replies_count = $tweet->replies_count + 1;
        } else {
            $tweet->replies_count = $tweet->replies_count - 1;
        }
        $tweet->save();
    }
}
