<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\DeviceManagerController;

class TrackDeviceActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Track device activity for authenticated users
        if (auth()->check()) {
            DeviceManagerController::updateLastActivity();
        }

        return $next($request);
    }
}
