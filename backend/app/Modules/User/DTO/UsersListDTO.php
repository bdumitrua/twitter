<?php

namespace App\Modules\User\DTO;

class UsersListDTO
{
    public ?string $name = null;
    public ?string $description = null;
    public ?string $bgImage = null;
    public ?bool $isPrivate = false;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'bgImage' => $this->bgImage,
            'isPrivate' => $this->name,
        ];
    }
}
