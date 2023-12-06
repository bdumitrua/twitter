<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\AuthRegistration;
use App\Modules\Auth\Models\AuthReset;
use App\Modules\Auth\Requests\AuthConfirmCodeRequest;
use App\Modules\Auth\Requests\CheckEmailRequest;
use App\Modules\Auth\Requests\CreateUserRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\PasswordRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function registrationStart(CreateUserRequest $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->authService->registrationStart($request);
        });
    }

    public function registrationConfirm(AuthRegistration $authRegistration, AuthConfirmCodeRequest $request)
    {
        return $this->handleServiceCall(function () use ($authRegistration, $request) {
            return $this->authService->registrationConfirm($authRegistration, $request);
        });
    }

    public function registrationEnd(AuthRegistration $authRegistration, PasswordRequest $request)
    {
        return $this->handleServiceCall(function () use ($authRegistration, $request) {
            return $this->authService->registrationEnd($authRegistration, $request);
        });
    }

    public function resetCheck(CheckEmailRequest $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->authService->resetCheck($request);
        });
    }

    public function resetConfirm(AuthReset $authReset, AuthConfirmCodeRequest $request)
    {
        return $this->handleServiceCall(function () use ($authReset, $request) {
            return $this->authService->resetConfirm($authReset, $request);
        });
    }

    public function resetEnd(AuthReset $authReset, PasswordRequest $request)
    {
        return $this->handleServiceCall(function () use ($authReset, $request) {
            return $this->authService->resetEnd($authReset, $request);
        });
    }

    public function login(LoginRequest $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->authService->login($request);
        });
    }

    public function logout(Request $request)
    {
        return $this->handleServiceCall(function () use ($request) {
            return $this->authService->logout($request);
        });
    }

    public function refresh()
    {
        return $this->handleServiceCall(function () {
            return $this->authService->refresh();
        });
    }
}
