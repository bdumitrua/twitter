<?php

namespace App\Modules\User\Listeners;

class NewNotificationListener
{
    public function handle($event)
    {
        $data = $event->data;
    }
}
