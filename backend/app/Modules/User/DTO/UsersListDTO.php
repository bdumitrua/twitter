<?php

namespace App\Modules\User\DTO;

class UsersListDTO
{
    public $name;
    public $description;
    public $bgImage;
    public $isPrivate;

    public function __construct(
        string $name,
        string $description,
        string $bgImage,
        bool $isPrivate
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->bgImage = $bgImage;
        $this->isPrivate = $isPrivate;
    }
}
