<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidTokenException extends HttpException
{
    protected $code = Response::HTTP_FORBIDDEN;
    protected $message = "Token is invalid";

    public function __construct()
    {
        parent::__construct($this->code, $this->message);
    }
}
