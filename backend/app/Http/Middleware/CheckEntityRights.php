<?php

namespace App\Http\Middleware;

use App\Exceptions\AccessDeniedException;
use App\Modules\User\Models\UserGroup;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckEntityRights
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string $entityName
     * 
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $entityName): Response
    {
        /** @var UserGroup */
        $entity = $request->route($entityName);

        if ($entity->user_id !== Auth::id()) {
            throw new AccessDeniedException();
        }

        return $next($request);
    }
}
