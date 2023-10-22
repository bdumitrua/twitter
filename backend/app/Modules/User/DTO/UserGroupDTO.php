<?php

namespace App\Modules\User\DTO;

use App\Modules\Base\BaseDTO;

class UserGroupDTO extends BaseDTO
{
    public ?string $name = null;
    public ?string $description = null;
}
