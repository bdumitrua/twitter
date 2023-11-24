<?php

namespace App\Helpers;

class StringHelper
{
    public static function camelToSnake(string $string): string
    {
        $pattern = '/([a-z])([A-Z])/';
        $replacement = '$1_$2';
        $snakeCase = strtolower(preg_replace($pattern, $replacement, $string));

        return $snakeCase;
    }

    public static function snakeToCamel(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }
}
