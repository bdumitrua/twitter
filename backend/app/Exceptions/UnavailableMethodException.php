<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UnavailableMethodException extends HttpException
{
    protected $code = Response::HTTP_I_AM_A_TEAPOT;
    protected $message = "Unavailable method";

    public function __construct(?string $message)
    {
        if (!empty($message)) {
            $this->message = $message;
        }

        parent::__construct($this->code, $this->message);
    }
}
