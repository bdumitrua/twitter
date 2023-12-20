<?php

namespace App\Modules\Notification\Listeners;

class NewNotificationListener
{
    public function handle($event)
    {
        $data = $event->data;
    }
}
