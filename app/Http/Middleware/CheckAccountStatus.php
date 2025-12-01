<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        $currentRoute = $request->route()->getName();
        $userStatus = strtolower($user->status->status ?? 'active');

        // List of routes allowed for pending users
        $pendingAllowedRoutes = [
            'admin.profile',
            'admin.profile.edit',
            'admin.profile.update',
            'enforcer.profile',
            'enforcer.profile.edit',
            'enforcer.profile.update',
            'front-desk.profile',
            'front-desk.profile.edit',
            'front-desk.profile.update',
            'logout',
            'profile',
            'devices.index',
        ];

        // If user is pending and trying to access a restricted route
        if ($userStatus === 'pending' && !in_array($currentRoute, $pendingAllowedRoutes)) {
            // For AJAX requests, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Your account is pending approval. Only profile access is available.',
                    'pending' => true
                ], 403);
            }

            // For regular requests, redirect to profile
            return redirect()->route(
                $user->role_id === 2 ? 'enforcer.profile' : 'admin.profile'
            )->with('warning', 'Your account is pending approval. You can only access your profile.');
        }

        // Block rejected users
        if ($userStatus === 'rejected') {
            auth()->logout();
            return redirect()->route('login.form')
                ->withErrors(['login' => 'Your account has been rejected. Please contact the administrator.']);
        }

        // Block suspended users
        if ($userStatus === 'suspended') {
            auth()->logout();
            return redirect()->route('login.form')
                ->withErrors(['login' => 'Your account has been suspended. Please contact the administrator.']);
        }

        return $next($request);
    }
}
