<?php

namespace App\Modules\User\Listeners;

use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Models\TwittLike;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UserSubscribtion;

class UpdateTwittLikesCount
{
    public function handle($event)
    {
        /** @var TwittLike */
        $twittLike = $event->twittLike;
        $add = $event->add;

        $twitt = Twitt::find($twittLike->twitt_id);
        if (!empty($add)) {
            $twitt->likes_count = $twitt->likes_count + 1;
        } else {
            $twitt->likes_count = $twitt->likes_count - 1;
        }
        $twitt->save();
    }
}
