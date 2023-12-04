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

    public static function createUserLink(string $email): string
    {
        $link = strstr($email, '@', true);
        $link = preg_replace("/[^\w]/", "", $link);
        if (strlen($link) > 15) {
            $link = substr($link, 0, 15);
        }
        $link = $link . (string)rand(10000, 99999);

        return $link;
    }
}
