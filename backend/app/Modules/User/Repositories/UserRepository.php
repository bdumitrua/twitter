<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    // Base method example
    public function findById($id)
    {
        return $this->user->find($id);
    }
}
