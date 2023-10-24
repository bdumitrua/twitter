<?php

namespace App\Modules\User\Listeners;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Models\TweetLike;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UserSubscribtion;

class UpdateTweetLikesCount
{
    public function handle($event)
    {
        /** @var TweetLike */
        $tweetLike = $event->tweetLike;
        $add = $event->add;

        $tweet = Tweet::find($tweetLike->tweet_id);
        if (!empty($add)) {
            $tweet->likes_count = $tweet->likes_count + 1;
        } else {
            $tweet->likes_count = $tweet->likes_count - 1;
        }
        $tweet->save();
    }
}
