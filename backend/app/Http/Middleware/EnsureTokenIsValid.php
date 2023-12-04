<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class EnsureTokenIsValid
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Проверка даты инвалидации токена
        if ($user->token_invalid_before && JWTAuth::getPayload(JWTAuth::getToken())->get('iat') < $user->token_invalid_before->timestamp) {
            Auth::logout(true);
            return response()->json(['error' => 'Token is invalid'], 401);
        }

        return $next($request);
    }
}
