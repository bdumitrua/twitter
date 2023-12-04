<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

// 422Unprocessable
class AccessDeniedException extends HttpException
{
    protected $code = Response::HTTP_FORBIDDEN;
    protected $message = "Access denied";

    public function __construct()
    {
        parent::__construct($this->code, $this->message);
    }
}
