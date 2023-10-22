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

        $twitt = Twitt::find($twittFavorite->twitt_id);
        $twitt->favorites_count = TwittFavorite::where('twitt_id', $twitt->id)->count();
        $twitt->save();
    }
}
