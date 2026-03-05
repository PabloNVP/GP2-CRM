<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        $userRole = $user?->role;

        if ($userRole instanceof RoleEnum) {
            $userRole = $userRole->value;
        }

        if (! $user || ! in_array($userRole, $roles, true)) {
            throw new HttpResponseException(response('Unauthorized', 403));
        }

        return $next($request);
    }
}
