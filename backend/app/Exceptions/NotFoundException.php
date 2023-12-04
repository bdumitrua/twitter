<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundException extends HttpException
{
    protected $code = Response::HTTP_NOT_FOUND;
    protected $message = "Entity not found";

    public function __construct(string $entityName)
    {
        $this->message = "{$entityName} not found";

        parent::__construct($this->code, $this->message);
    }
}
