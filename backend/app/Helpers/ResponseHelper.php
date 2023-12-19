<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class ResponseHelper
{
    public static function okResponse(bool $didAction = true): Response
    {
        return $didAction
            ? self::successResponse()
            : self::noContent();
    }

    protected static function successResponse(): Response
    {
        return response(null, 200);
    }

    public static function noContent(): Response
    {
        return response("No content", 204);
    }
}
