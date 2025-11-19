<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEnforcer
{
    /**
     * Handle an incoming request.
     * Allow only users with role name 'Enforcer'.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user) {
            // Not authenticated - let auth middleware handle normally.
            abort(403, 'Forbidden');
        }

        // If role relationship exists and name equals 'Enforcer' (case-insensitive)
        if (isset($user->role) && strtolower($user->role->name ?? '') === 'enforcer') {
            return $next($request);
        }

        // If user has a role_name or role_name attribute
        if (isset($user->role_name) && strtolower($user->role_name) === 'enforcer') {
            return $next($request);
        }

        abort(403, 'Forbidden');
    }
}
