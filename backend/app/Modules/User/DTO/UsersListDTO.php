<?php

namespace App\Modules\User\DTO;

use App\Modules\Base\BaseDTO;

class UsersListDTO extends BaseDTO
{
    public ?string $name = null;
    public ?string $description = null;
    public ?string $bgImage = null;
    public ?bool $isPrivate = false;
}
