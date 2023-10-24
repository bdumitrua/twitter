<?php

namespace App\Modules\User\Listeners;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Models\TweetFavorite;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UserSubscribtion;

class UpdateTweetFavoritesCount
{
    public function handle($event)
    {
        /** @var TweetFavorite */
        $tweetFavorite = $event->tweetFavorite;
        $add = $event->add;

        $tweet = Tweet::find($tweetFavorite->tweet_id);
        if (!empty($add)) {
            $tweet->favorites_count = $tweet->favorites_count + 1;
        } else {
            $tweet->favorites_count = $tweet->favorites_count - 1;
        }
        $tweet->save();
    }
}
