<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Services\UserService;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Method realization example
    public function show(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->userService->show($user);
        });
    }
}