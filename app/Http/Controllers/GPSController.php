<?php

namespace App\Http\Controllers;

use App\Models\EnforcerLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GPSController extends Controller
{
    /**
     * Update enforcer's GPS location
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|integer',
            'address' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Check if user is an enforcer
        if (strtolower($user->role->name ?? '') !== 'enforcer') {
            return response()->json(['error' => 'Only enforcers can update location'], 403);
        }

        // Check if location tracking is enabled
        if (!$user->location_tracking_enabled) {
            return response()->json(['error' => 'Location tracking is not enabled'], 403);
        }

        try {
            // Create new location record
            $location = EnforcerLocation::create([
                'user_id' => $user->id,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'accuracy_meters' => $validated['accuracy'],
                'address' => $validated['address'],
                'status' => 'online',
            ]);

            // Delete old location records (keep only last 100)
            EnforcerLocation::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->offset(100)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully',
                'location' => $location,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update location: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get enforcer's current location
     */
    public function getCurrentLocation(): JsonResponse
    {
        $user = auth()->user();

        if (strtolower($user->role->name ?? '') !== 'enforcer') {
            return response()->json(['error' => 'Only enforcers can access this'], 403);
        }

        $location = EnforcerLocation::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$location) {
            return response()->json(['error' => 'No location data found'], 404);
        }

        return response()->json($location);
    }

    /**
     * Get all online enforcers (for admin dashboard)
     */
    public function getOnlineEnforcers(): JsonResponse
    {
        $thirtyMinutesAgo = now()->subMinutes(30);

        $onlineEnforcers = EnforcerLocation::with('user')
            ->where('status', 'online')
            ->where('created_at', '>=', $thirtyMinutesAgo)
            ->latest('created_at')
            ->get()
            ->groupBy('user_id')
            ->map(function ($locations) {
                return $locations->first();
            })
            ->values();

        return response()->json([
            'success' => true,
            'count' => $onlineEnforcers->count(),
            'enforcers' => $onlineEnforcers,
        ]);
    }

    /**
     * Get enforcer location history
     */
    public function getLocationHistory(User $user, Request $request): JsonResponse
    {
        $hours = $request->query('hours', 24);

        $locations = EnforcerLocation::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $locations->count(),
            'locations' => $locations,
        ]);
    }

    /**
     * Set enforcer status (online/offline/on_break)
     */
    public function setStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:online,offline,on_break',
        ]);

        $user = auth()->user();

        if (strtolower($user->role->name ?? '') !== 'enforcer') {
            return response()->json(['error' => 'Only enforcers can update status'], 403);
        }

        try {
            // Update all recent locations with new status
            EnforcerLocation::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subHours(1))
                ->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated to: ' . $validated['status'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }
}
