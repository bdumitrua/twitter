<?php

namespace App\Modules\User\DTO;

class UserGroupDTO
{
    public $name;
    public $description;

    public function __construct(
        string $name,
        string $description
    ) {
        $this->name = $name;
        $this->description = $description;
    }
}
