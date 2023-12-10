<?php

namespace App\Modules\Auth\Services;

use App\Exceptions\CodeNotConfirmedException;
use App\Exceptions\IncorrectCodeException;
use App\Exceptions\InvalidCredetialsException;
use App\Exceptions\NotFoundException;
use App\Helpers\StringHelper;
use App\Modules\Auth\Events\PasswordResetStartedEvent;
use App\Modules\Auth\Models\AuthRegistration;
use App\Modules\Auth\Models\AuthReset;
use App\Modules\Auth\Requests\CreateUserRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\PasswordRequest;
use App\Modules\Auth\Requests\AuthConfirmCodeRequest;
use App\Modules\Auth\Requests\CheckEmailRequest;
use App\Modules\Auth\Resources\AuthTokenResource;
use App\Modules\User\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function registrationStart(CreateUserRequest $request)
    {
        $registrationCode = $this->createUniqueCode();
        $userEmail = $request->email;

        Log::info('Starting user registration', $request->toArray());
        $registrationData = AuthRegistration::create([
            'code' => $registrationCode,
            'name' => $request->name,
            'email' => $userEmail,
            'birth_date' => $request->birth_date,
        ]);

        return ['registration_id' => $registrationData->id];
    }

    public function registrationConfirm(AuthRegistration $authRegistration, AuthConfirmCodeRequest $request): void
    {
        Log::info('Confirming registration code', ['id' => $authRegistration->id]);
        if ($request->code !== $authRegistration->code) {
            throw new IncorrectCodeException();
        }

        $authRegistration->confirmed = true;
        $authRegistration->save();
        Log::info('Confirmed registration code', ['id' => $authRegistration->id]);
    }

    public function registrationEnd(AuthRegistration $authRegistration, PasswordRequest $request): void
    {
        if (empty($authRegistration->confirmed)) {
            throw new CodeNotConfirmedException();
        }

        $userEmail = $authRegistration->email;
        $link = StringHelper::createUserLink($userEmail);

        Log::info(
            'Creating user at last registration step',
            [
                'authRegistration' => $authRegistration->toArray(),
                'request' => $request->toArray()
            ]
        );
        User::create([
            'link' => $link,
            'email' => $userEmail,
            'name' => $authRegistration->name,
            'password' => Hash::make($request->password),
            'birth_date' => $authRegistration->birth_date,
        ]);

        Log::info('Deleting all registration datxa after registration', ['email' => $userEmail]);
        AuthRegistration::where('email', $userEmail)->delete();
    }

    public function resetCheck(CheckEmailRequest $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (empty($user)) {
            throw new NotFoundException('Account');
        }

        Log::info('Starting reset user password', ['user_id' => $user->id]);
        $resetCode = $this->createUniqueCode();
        $resetData = AuthReset::create([
            'code' => $resetCode,
            'user_id' => $user->id,
        ]);
        event(new PasswordResetStartedEvent($resetData, $email));

        return ['reset_id' => $resetData->id];
    }

    public function resetConfirm(AuthReset $authReset, AuthConfirmCodeRequest $request)
    {
        Log::info('Confirming reset code', ['id' => $authReset->id]);
        if ($request->code !== $authReset->code) {
            throw new IncorrectCodeException();
        }

        $authReset->confirmed = true;
        $authReset->save();
        Log::info('Confirmed reset code', ['id' => $authReset->id]);
    }

    public function resetEnd(AuthReset $authReset, PasswordRequest $request)
    {
        if (empty($authReset->confirmed)) {
            throw new CodeNotConfirmedException();
        }

        $user = $authReset->user;

        $user->password = Hash::make($request->password);
        $user->token_invalid_before = now();

        $user->save();
        Log::info('Changed user password', ['user_id' => $user->id]);

        Log::info('Deleting all reset password data after succesfull reset', ['user_id' => $user->id]);
        AuthRegistration::where('email', $user->email)->delete();
    }


    public function login(LoginRequest $request): JsonResource
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            throw new InvalidCredetialsException();
        }

        Log::info('Authorized user by email and password', ['email' => $request->email, 'ip' => $request->ip()]);
        return new AuthTokenResource($token);
    }

    public function logout($request): void
    {
        Auth::logout();
        Log::info('User exited from account', ['user' => Auth::user(), 'ip' => $request->ip()]);
    }

    public function refresh(): JsonResource
    {
        return new AuthTokenResource(Auth::refresh());
    }

    protected function createUniqueCode(): string
    {
        return '11111';
    }
}
