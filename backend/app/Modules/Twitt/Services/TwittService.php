<?php

namespace App\Modules\Twitt\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Repositories\TwittRepository;

class TwittService
{
    private $twittRepository;

    public function __construct(
        TwittRepository $twittRepository
    ) {
        $this->twittRepository = $twittRepository;
    }
}
