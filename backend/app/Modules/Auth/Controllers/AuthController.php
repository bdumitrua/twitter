<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\AuthRegistration;
use App\Modules\Auth\Requests\CreateUserRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\PasswordRequest;
use App\Modules\Auth\Requests\RegistrationCodeRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function start(CreateUserRequest $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->authService->start($request);
        });
    }

    public function confirm(AuthRegistration $authRegistration, RegistrationCodeRequest $request)
    {
        return $this->handleServiceCall(function () use ($authRegistration, $request) {
            return $this->authService->confirm($authRegistration, $request);
        });
    }

    public function register(AuthRegistration $authRegistration, PasswordRequest $request)
    {
        return $this->handleServiceCall(function () use ($authRegistration, $request) {
            return $this->authService->register($authRegistration, $request);
        });
    }

    public function login(LoginRequest $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->authService->login($request);
        });
    }

    public function logout()
    {
        return $this->handleServiceCall(function () {
            return $this->authService->logout();
        });
    }

    public function refresh()
    {
        return $this->handleServiceCall(function () {
            return $this->authService->refresh();
        });
    }
}
