<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class AppEnvironmentHelper
{
    public static function isTesting(): bool
    {
        return app()->environment('testing');
    }
}
