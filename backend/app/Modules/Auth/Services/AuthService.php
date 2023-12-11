<?php

namespace App\Modules\Auth\Services;

use App\Exceptions\CodeNotConfirmedException;
use App\Exceptions\IncorrectCodeException;
use App\Exceptions\InvalidCredetialsException;
use App\Exceptions\InvalidTokenException;
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
use App\Modules\Auth\Resources\PasswordResetCodeResource;
use App\Modules\Auth\Resources\PasswordResetConfirmedResource;
use App\Modules\Auth\Resources\RegistrationCodeResource;
use App\Modules\Auth\Resources\RegistrationConfirmedResource;
use App\Modules\User\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthService
{
    /**
     * @param CreateUserRequest $request
     * 
     * @return JsonResource
     */
    public function registrationStart(CreateUserRequest $request): JsonResource
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

        return new RegistrationCodeResource($registrationData->id);
    }

    /**
     * @param AuthRegistration $authRegistration
     * @param AuthConfirmCodeRequest $request
     * 
     * @return JsonResource
     * 
     * @throws IncorrectCodeException
     */
    public function registrationConfirm(AuthRegistration $authRegistration, AuthConfirmCodeRequest $request): JsonResource
    {
        $registrationId = $authRegistration->id;
        Log::info('Confirming registration code', ['id' => $registrationId]);
        if ($request->code !== $authRegistration->code) {
            throw new IncorrectCodeException();
        }

        $authRegistration->confirmed = true;
        $authRegistration->save();
        Log::info('Confirmed registration code', ['id' => $registrationId]);

        return new RegistrationConfirmedResource($registrationId);
    }

    /**
     * @param AuthRegistration $authRegistration
     * @param PasswordRequest $request
     * 
     * @return void
     * 
     * @throws CodeNotConfirmedException
     */
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

    /**
     * @param CheckEmailRequest $request
     * 
     * @return JsonResource
     * 
     * @throws NotFoundException
     */
    public function resetCheck(CheckEmailRequest $request): JsonResource
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

        return new PasswordResetCodeResource($resetData->id);
    }

    /**
     * @param AuthReset $authReset
     * @param AuthConfirmCodeRequest $request
     * 
     * @return JsonResource
     * 
     * @throws IncorrectCodeException
     */
    public function resetConfirm(AuthReset $authReset, AuthConfirmCodeRequest $request): JsonResource
    {
        $resetId = $authReset->id;
        Log::info('Confirming reset code', ['id' => $resetId]);
        if ($request->code !== $authReset->code) {
            throw new IncorrectCodeException();
        }

        $authReset->confirmed = true;
        $authReset->save();
        Log::info('Confirmed reset code', ['id' => $resetId]);

        return new PasswordResetConfirmedResource($resetId);
    }

    /**
     * @param AuthReset $authReset
     * @param PasswordRequest $request
     * 
     * @return void
     * 
     * @throws CodeNotConfirmedException
     */
    public function resetEnd(AuthReset $authReset, PasswordRequest $request): void
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


    /**
     * @param LoginRequest $request
     * 
     * @return JsonResource
     * 
     * @throws InvalidCredetialsException
     */
    public function login(LoginRequest $request): JsonResource
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            throw new InvalidCredetialsException();
        }

        Log::info('Authorized user by email and password', ['email' => $request->email, 'ip' => $request->ip()]);
        return new AuthTokenResource($token);
    }

    /**
     * @param mixed $request
     * 
     * @return void
     */
    public function logout($request): void
    {
        Auth::logout();
        Log::info('User exited from account', ['user' => Auth::user(), 'ip' => $request->ip()]);
    }

    /**
     * @return JsonResource
     * 
     * @throws InvalidTokenException
     */
    public function refresh(): JsonResource
    {
        try {
            $newToken = new AuthTokenResource(Auth::refresh());
            return $newToken;
        } catch (TokenBlacklistedException $e) {
            // From 502 to 403
            throw new InvalidTokenException();
        }
    }

    /**
     * @return string
     */
    protected function createUniqueCode(): string
    {
        return '11111';
    }
}
