<?php

namespace App\Modules\User\DTO;

class UserGroupDTO
{
    public $userId;
    public $name;
    public $description;

    public function __construct(
        int $userId,
        string $name,
        string $description
    ) {
        $this->userId = $userId;
        $this->name = $name;
        $this->description = $description;
    }
}
