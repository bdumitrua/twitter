<?php

namespace App\Modules\Notification\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Notification\Events\NewNotificationEvent;

class NotifyAboutNewNotificationEvent
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewNotificationEvent $newNotificationEvent): void
    {
        //
    }
}
