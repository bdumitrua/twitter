<?php

namespace App\Modules\Message\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Message\Events\NewMessageEvent;

class NotifyAboutNewMessageEvent
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
    public function handle(NewMessageEvent $newMessageEvent): void
    {
        //
    }
}
