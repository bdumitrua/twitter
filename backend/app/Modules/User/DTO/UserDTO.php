<?php

namespace App\Modules\User\DTO;

class UserDTO
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

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'link' => $this->link,
            'email' => $this->email,
            'bgImage' => $this->bgImage,
            'avatar' => $this->avatar,
            'statusText' => $this->statusText,
            'siteUrl' => $this->siteUrl,
            'address' => $this->address,
            'birthDate' => $this->birthDate,
        ];
    }
}
