<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class ResponseHelper
{
    public static function okResponse(bool $noContent = false): Response
    {
        return $noContent
            ? self::noContent()
            : self::successResponse();
    }

    protected static function successResponse(): Response
    {
        return response(null, 200);
    }

    public static function noContent(): Response
    {
        return response(null, 204);
    }
}
