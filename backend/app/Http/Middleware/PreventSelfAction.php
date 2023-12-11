<?php

namespace App\Http\Middleware;

use App\Exceptions\AccessDeniedException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PreventSelfAction
{
    /**
     * @param Request $request
     * @param Closure $next
     * 
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var UserGroup */
        $user = $request->route('user');

        if ($user->id === Auth::id()) {
            throw new AccessDeniedException();
        }

        return $next($request);
    }
}
