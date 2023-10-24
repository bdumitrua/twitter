<?php

namespace App\Modules\User\Listeners;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UserSubscribtion;

class UpdateTweetRepostsCount
{
    public function handle($event)
    {
        /** @var Tweet */
        $tweetId = $event->tweetId;
        $add = $event->add;

        $tweet = Tweet::find($tweetId);
        if (!empty($add)) {
            $tweet->reposts_count = $tweet->reposts_count + 1;
        } else {
            $tweet->reposts_count = $tweet->reposts_count - 1;
        }
        $tweet->save();
    }
}
