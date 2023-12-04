<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IncorrectCodeException extends HttpException
{
    protected $code = Response::HTTP_BAD_REQUEST;
    protected $message = "Incorrect code";

    public function __construct()
    {
        parent::__construct($this->code, $this->message);
    }
}
