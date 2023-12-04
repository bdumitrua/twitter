<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);
        $user = Auth::user();

        // Проверка даты инвалидации токена
        if ($user->token_invalid_before && JWTAuth::getPayload(JWTAuth::getToken())->get('iat') < $user->token_invalid_before->timestamp) {
            Auth::logout(true);
            return response()->json(['error' => 'Token is invalid'], 401);
        }

        return $next($request);
    }
}
