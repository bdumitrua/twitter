<?php

namespace App\Modules\User\Listeners;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;

class NewUsersListener
{
    public function handle($event)
    {
        $data = $event->data;
    }
}