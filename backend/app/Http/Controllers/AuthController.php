<?php

namespace App\Http\Controllers;

use App\AuthRegistration;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmRegistrationRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\RegistrationCodeRequest;
use App\Http\Requests\RegistrationRequest;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    public function start(CreateUserRequest $request)
    {
        $registrationData = AuthRegistration::create([
            // * Оставил 11111 для удобства разработки, сделать 5 рандомных символов не трудно
            'code' => '11111',
            'name' => $request->name,
            'email' => $request->email,
            'birth_date' => $request->birth_date,
        ]);

        return response()->json('Registration id: ' . $registrationData->id);
    }

    public function confirm(AuthRegistration $authRegistration, RegistrationCodeRequest $request)
    {
        if ($request->code !== $authRegistration->code) {
            throw new HttpException(403, 'Incorrect code');
        }

        $authRegistration->confirmed = true;
        $authRegistration->save();

        return response()->json('Registration confirmed successfully');
    }

    public function register(AuthRegistration $authRegistration, PasswordRequest $request)
    {
        if (empty($authRegistration->confirmed)) {
            throw new HttpException(403, 'Registration code not confirmed');
        }

        User::create([
            'password' => Hash::make($request->password),
            'name' => $authRegistration->name,
            'link' => $authRegistration->name . Str::random(8),
            'email' => $authRegistration->email,
            'birth_date' => $authRegistration->birth_date,
        ]);

        return response()->json('User created successfully');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        Auth::logout();
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token_type' => 'bearer',
            'access_token' => $token,
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
