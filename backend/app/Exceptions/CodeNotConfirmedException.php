<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CodeNotConfirmedException extends HttpException
{
    protected $code = Response::HTTP_FORBIDDEN;
    protected $message = "Action code not confirmed";

    public function __construct()
    {
        parent::__construct($this->code, $this->message);
    }
}
