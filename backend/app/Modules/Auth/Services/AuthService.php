<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Events\UserCreatedEvent;
use App\Modules\Auth\Models\AuthRegistration;
use App\Modules\Auth\Requests\CreateUserRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\PasswordRequest;
use App\Modules\Auth\Requests\RegistrationCodeRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\Auth\Resources\AuthTokenResource;
use App\Modules\User\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function registrationStart(CreateUserRequest $request): array
    {
        $registrationData = AuthRegistration::create([
            // * Оставил 11111 для удобства разработки, сделать 5 рандомных символов не трудно
            'code' => '11111',
            'name' => $request->name,
            'email' => $request->email,
            'birth_date' => $request->birth_date,
        ]);

        return ['registration_id' => $registrationData->id];
    }

    public function registrationConfirm(AuthRegistration $authRegistration, RegistrationCodeRequest $request): void
    {
        if ($request->code !== $authRegistration->code) {
            throw new HttpException(403, 'Incorrect code');
        }

        $authRegistration->confirmed = true;
        $authRegistration->save();
    }

    public function registrationEnd(AuthRegistration $authRegistration, PasswordRequest $request): void
    {
        if (empty($authRegistration->confirmed)) {
            throw new HttpException(403, 'Registration code not confirmed');
        }

        $user = User::create([
            'password' => Hash::make($request->password),
            'name' => $authRegistration->name,
            'link' => $authRegistration->name . Str::random(8),
            'email' => $authRegistration->email,
            'birth_date' => $authRegistration->birth_date,
        ]);

        event(new UserCreatedEvent($user));
    }

    public function resetCheck(CreateUserRequest $request)
    {
        // 
    }

    public function resetConfirm(AuthRegistration $authRegistration, RegistrationCodeRequest $request)
    {
        // 
    }

    public function resetEnd(AuthRegistration $authRegistration, PasswordRequest $request)
    {
        // 
    }

    public function login(LoginRequest $request): JsonResource
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            throw new HttpException(400, 'Invalid credentials');
        }

        return new AuthTokenResource($token);
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function refresh(): JsonResource
    {
        return new AuthTokenResource(Auth::refresh());
    }
}
