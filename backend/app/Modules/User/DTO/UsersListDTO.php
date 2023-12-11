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
            'bg_image' => $this->bgImage,
            'is_private' => $this->isPrivate,
        ];
    }
}
