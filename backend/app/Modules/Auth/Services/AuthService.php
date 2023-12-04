<?php

namespace App\Modules\Auth\Services;

use App\Helpers\StringHelper;
use App\Modules\Auth\Events\UserCreatedEvent;
use App\Modules\Auth\Models\AuthRegistration;
use App\Modules\Auth\Models\AuthReset;
use App\Modules\Auth\Requests\CreateUserRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\PasswordRequest;
use App\Modules\Auth\Requests\AuthConfirmCodeRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\Auth\Requests\CheckEmailRequest;
use App\Modules\Auth\Resources\AuthTokenResource;
use App\Modules\User\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Nette\Utils\Random;

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

    public function registrationConfirm(AuthRegistration $authRegistration, AuthConfirmCodeRequest $request): void
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

        $userEmail = $authRegistration->email;
        $link = StringHelper::createUserLink($userEmail);

        $user = User::create([
            'link' => $link,
            'email' => $userEmail,
            'name' => $authRegistration->name,
            'password' => Hash::make($request->password),
            'birth_date' => $authRegistration->birth_date,
        ]);

        event(new UserCreatedEvent($user));
    }

    public function resetCheck(CheckEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (empty($user)) {
            throw new HttpException(404, 'Account with this email doesn\'t exist');
        }

        $resetData = AuthReset::create([
            // * Оставил 11111 для удобства разработки, сделать 5 рандомных символов не трудно
            'code' => '11111',
            'user_id' => $user->id,
        ]);

        return ['reset_id' => $resetData->id];
    }

    public function resetConfirm(AuthReset $authReset, AuthConfirmCodeRequest $request)
    {
        if ($request->code !== $authReset->code) {
            throw new HttpException(403, 'Incorrect code');
        }

        $authReset->confirmed = true;
        $authReset->save();
    }

    public function resetEnd(AuthReset $authReset, PasswordRequest $request)
    {
        if (empty($authReset->confirmed)) {
            throw new HttpException(403, 'Registration code not confirmed');
        }

        $user = $authReset->user;

        $user->password = Hash::make($request->password);
        $user->token_invalid_before = now();

        $user->save();
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
