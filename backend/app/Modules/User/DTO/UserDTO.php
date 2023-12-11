<?php

namespace App\Modules\User\DTO;

class UserDTO
{
    public ?string $name = null;
    public ?string $link = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $about = null;
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
            'about' => $this->about,
            'bg_image' => $this->bgImage,
            'avatar' => $this->avatar,
            'status_text' => $this->statusText,
            'site_url' => $this->siteUrl,
            'address' => $this->address,
            'birth_date' => $this->birthDate,
        ];
    }
}
