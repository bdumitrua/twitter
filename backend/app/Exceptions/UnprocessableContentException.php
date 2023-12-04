<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UnprocessableContentException extends HttpException
{
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
    protected $message = "Unprocessable content";

    public function __construct(string $message)
    {
        $this->message = $message;

        parent::__construct($this->code, $this->message);
    }
}
