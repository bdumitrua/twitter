<?php

namespace App\Modules\Twitt\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Twitt\Events\NewTwittEvent;

class NotifyAboutNewTwittEvent
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
    public function handle(NewTwittEvent $newTwittEvent): void
    {
        //
    }
}
