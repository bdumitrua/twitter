<?php

namespace App\Modules\User\DTO;

class UserGroupDTO
{
    public ?string $name = null;
    public ?string $description = null;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
