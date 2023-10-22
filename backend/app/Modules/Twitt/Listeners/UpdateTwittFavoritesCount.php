<?php

namespace App\Modules\User\Listeners;

use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Models\TwittFavorite;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UserSubscribtion;

class UpdateTwittFavoritesCount
{
    public function handle($event)
    {
        /** @var TwittFavorite */
        $twittFavorite = $event->twittFavorite;
        $add = $event->add;

        $twitt = Twitt::find($twittFavorite->twitt_id);
        if (!empty($add)) {
            $twitt->favorites_count = $twitt->favorites_count + 1;
        } else {
            $twitt->favorites_count = $twitt->favorites_count - 1;
        }
        $twitt->save();
    }
}
