<?php

namespace App\Modules\User\Listeners;

use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Models\TwittLike;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UserSubscribtion;

class UpdateGroupMembersCount
{
    public function handle($event)
    {
        /** @var TwittLike */
        $twittLike = $event->twittLike;

        $twitt = Twitt::find($twittLike->twitt_id);
        $twitt->favorites_count = TwittLike::where('twitt_id', $twitt->id)->count();
        $twitt->save();
    }
}
