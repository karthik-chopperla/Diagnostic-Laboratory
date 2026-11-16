<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleAccess
{
    /**
     * Handle an incoming request and ensure user has role permission for the route.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $routeName = $request->route()?->getName();

        // Check if user has permission to access the named route
        if ($routeName && !$user->canAccessRoute($routeName)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => "Access Restricted: Role '{$user->role}' cannot access this resource.",
                ], 403);
            }

            return redirect()->route('dashboard')->with(
                'error',
                "Access Restricted: Your current role [{$user->role}] is not authorized to access this module."
            );
        }

        return $next($request);
    }
}

