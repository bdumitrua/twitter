<?php

namespace App\Modules\User\DTO;

class UsersListDTO
{
    public ?string $name = null;
    public ?string $description = null;
    public ?string $bgImage = null;
    public ?bool $isPrivate = false;
}
