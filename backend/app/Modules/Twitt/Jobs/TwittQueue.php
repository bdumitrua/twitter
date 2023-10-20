<?php

namespace App\Modules\Twitt\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TwittQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $twitt;
    
    public function __construct($twitt)
    {
        $this->twitt = $twitt;
    }

    public function handle()
    {
        // Implement your logic here
    }
}
