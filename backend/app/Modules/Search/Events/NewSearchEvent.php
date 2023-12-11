<?php

namespace App\Modules\Search\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewSearchEvent
{
    public $search;

    public function __construct($search)
    {
        $this->search = $search;
    }
}
