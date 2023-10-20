<?php
        
namespace App\Modules\Twitt\Repositories;

use App\Modules\Twitt\Models\Twitt;

class TwittRepository
{
    protected $twitt;

    public function __construct(Twitt $twitt)
    {
        $this->twitt = $twitt;
    }

    // Base method example
    public function findById($id)
    {
        return $this->twitt->find($id);
    }
}
