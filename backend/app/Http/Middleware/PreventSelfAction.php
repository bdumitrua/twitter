<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PreventSelfAction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var UserGroup */
        $user = $request->route('user');

        if ($user->id === Auth::id()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'You can\'t do this.');
        }

        return $next($request);
    }
}
