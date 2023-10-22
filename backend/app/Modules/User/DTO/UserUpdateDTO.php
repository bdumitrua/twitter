<?php

namespace App\Modules\User\DTO;

use App\Modules\Base\BaseDTO;

class UserUpdateDTO extends BaseDTO
{
    public ?string $name = null;
    public ?string $link = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $bgImage = null;
    public ?string $avatar = null;
    public ?string $statusText = null;
    public ?string $siteUrl = null;
    public ?string $address = null;
    public ?string $birthDate = null;
}
