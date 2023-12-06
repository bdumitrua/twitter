<?php

namespace App\Modules\Search\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Search\Events\NewSearchEvent;

class NewSearchListener
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
    public function handle(NewSearchEvent $newSearchEvent): void
    {
        //
    }
}
