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
            // Attempt to update a recent placeholder 'online' record first
            $lastLocation = EnforcerLocation::where('user_id', $user->id)
                ->latest()
                ->first();

            $shouldUpdateLast = false;

            if ($lastLocation) {
                // Consider it a placeholder if address indicates not available
                // or accuracy is very large (placeholder uses 5000)
                $isPlaceholderAddress = strcasecmp($lastLocation->address ?? '', 'Location not available') === 0;
                $isHighAccuracy = is_numeric($lastLocation->accuracy_meters) && $lastLocation->accuracy_meters >= 5000;

                // Only update recent placeholders (last 10 minutes)
                if (($isPlaceholderAddress || $isHighAccuracy) && $lastLocation->created_at >= now()->subMinutes(10)) {
                    $shouldUpdateLast = true;
                }
            }

            if ($shouldUpdateLast && $lastLocation) {
                $lastLocation->update([
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'accuracy_meters' => $validated['accuracy'],
                    'address' => $validated['address'],
                    'status' => 'online',
                ]);

                $location = $lastLocation->fresh();
            } else {
                // Create new location record
                $location = EnforcerLocation::create([
                    'user_id' => $user->id,
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'accuracy_meters' => $validated['accuracy'],
                    'address' => $validated['address'],
                    'status' => 'online',
                ]);
            }

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

        $userRole = strtolower($user->role->name ?? '');

        // Determine which user's location to fetch
        $targetUserId = $user->id;
        
        // If admin, they can view any location (from query param or authenticated user)
        if ($userRole === 'admin') {
            $targetUserId = request()->query('user_id', $user->id);
        } elseif ($userRole !== 'enforcer') {
            // Other roles can't access this
            return response()->json(['error' => 'Only enforcers can access this'], 403);
        }

        $location = EnforcerLocation::where('user_id', $targetUserId)
            ->latest()
            ->first();

        if (!$location) {
            return response()->json(['error' => 'No location data found'], 404);
        }

        return response()->json($location);
    }

    /**
     * Get all online enforcers (for admin dashboard)
     * Only returns enforcers whose LATEST location has status='online' AND is recent (< 30 seconds)
     */
    public function getOnlineEnforcers(): JsonResponse
    {
        $thirtySecondsAgo = now()->subSeconds(30);

        // Get all enforcers with their LATEST location (by updated_at so status changes count)
        $enforcers = User::with(['role', 'locations' => function($q) {
            $q->latest('updated_at')->limit(1);
        }])
            ->whereHas('role', function ($q) {
                $q->where('name', 'like', '%enforcer%');
            })
            ->get();

        $onlineEnforcers = collect();

        // Check each enforcer's LATEST status
        foreach ($enforcers as $enforcer) {
            if ($enforcer->locations->isNotEmpty()) {
                $lastLocation = $enforcer->locations->first();
                
                // Must be 'online' status AND recent (within 30 seconds)
                // Use updated_at because setStatus() may update status (and updated_at)
                if ($lastLocation->status === 'online' && $lastLocation->updated_at >= $thirtySecondsAgo) {
                    // Ensure the location record includes user data for frontend display
                    $lastLocation->setRelation('user', $enforcer->makeHidden(['password', 'remember_token']));
                    $onlineEnforcers->push($lastLocation);
                }
            }
        }

        return response()->json([
            'success' => true,
            'count' => $onlineEnforcers->count(),
            'enforcers' => $onlineEnforcers,
        ]);
    }

    /**
     * Get all recent enforcer locations (last 5 minutes) - for demo/local network
     */
    public function getRecentEnforcers(): JsonResponse
    {
        $fiveMinutesAgo = now()->subMinutes(5);

        // Get all enforcers with recent locations
        $recentEnforcers = EnforcerLocation::with('user')
            ->where('created_at', '>=', $fiveMinutesAgo)
            ->latest('created_at')
            ->get()
            ->unique('user_id')
            ->values();

        return response()->json([
            'success' => true,
            'count' => $recentEnforcers->count(),
            'enforcers' => $recentEnforcers,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get all enforcers with latest locations
     */
    public function getAllEnforcersLocations(): JsonResponse
    {
        // Get all enforcers
        $allEnforcers = User::with('role')
            ->whereHas('role', function ($q) {
                $q->where('name', 'like', '%enforcer%');
            })
            ->get();

        $enforcersWithLocations = collect();

        foreach ($allEnforcers as $enforcer) {
            $lastLocation = EnforcerLocation::with('user')
                ->where('user_id', $enforcer->id)
                ->latest('created_at')
                ->first();

            if ($lastLocation) {
                $enforcersWithLocations->push($lastLocation);
            }
        }

        return response()->json([
            'success' => true,
            'count' => $enforcersWithLocations->count(),
            'enforcers' => $enforcersWithLocations,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get location analytics for an enforcer
     */
    public function getLocationAnalytics(User $user, Request $request): JsonResponse
    {
        $hours = $request->query('hours', 24);

        $locations = EnforcerLocation::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderBy('created_at', 'asc')
            ->get();

        if ($locations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No location data available',
            ], 404);
        }

        // Calculate statistics
        $totalDistance = $this->calculateTotalDistance($locations);
        $totalTime = $locations->first()->created_at->diffInMinutes($locations->last()->created_at);
        $avgAccuracy = $locations->avg('accuracy_meters');
        $maxDistance = $this->calculateMaxDistance($locations);

        return response()->json([
            'success' => true,
            'enforcer' => $user->load('role'),
            'period_hours' => $hours,
            'location_count' => $locations->count(),
            'total_distance_km' => round($totalDistance, 2),
            'total_time_minutes' => $totalTime,
            'average_accuracy_m' => round($avgAccuracy, 2),
            'max_distance_from_start_km' => round($maxDistance, 2),
            'locations' => $locations,
            'first_location' => $locations->first(),
            'last_location' => $locations->last(),
        ]);
    }

    /**
     * Calculate total distance traveled
     */
    private function calculateTotalDistance($locations): float
    {
        if ($locations->count() < 2) {
            return 0;
        }

        $distance = 0;
        $prev = null;

        foreach ($locations as $location) {
            if ($prev) {
                $distance += $this->getDistanceFromCoordinates(
                    $prev->latitude, $prev->longitude,
                    $location->latitude, $location->longitude
                );
            }
            $prev = $location;
        }

        return $distance;
    }

    /**
     * Calculate maximum distance from starting point
     */
    private function calculateMaxDistance($locations): float
    {
        if ($locations->count() < 1) {
            return 0;
        }

        $start = $locations->first();
        $maxDistance = 0;

        foreach ($locations as $location) {
            $distance = $this->getDistanceFromCoordinates(
                $start->latitude, $start->longitude,
                $location->latitude, $location->longitude
            );
            $maxDistance = max($maxDistance, $distance);
        }

        return $maxDistance;
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     */
    private function getDistanceFromCoordinates(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371; // Earth radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
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

            // If setting to online and no recent locations exist, create one
            if ($validated['status'] === 'online') {
                $hasRecentLocation = EnforcerLocation::where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->exists();

                if (!$hasRecentLocation) {
                    // Get last known location or use default
                    $lastLocation = EnforcerLocation::where('user_id', $user->id)
                        ->latest()
                        ->first();

                    if ($lastLocation) {
                        // Create new record with last known location and online status
                        EnforcerLocation::create([
                            'user_id' => $user->id,
                            'latitude' => $lastLocation->latitude,
                            'longitude' => $lastLocation->longitude,
                            'accuracy_meters' => $lastLocation->accuracy_meters,
                            'address' => $lastLocation->address,
                            'status' => 'online',
                        ]);
                    } else {
                        // No location history - create placeholder location with Manila coordinates
                        // This allows enforcers without location access to still show as online
                        EnforcerLocation::create([
                            'user_id' => $user->id,
                            'latitude' => 14.5995,  // Manila, Philippines
                            'longitude' => 121.0012,
                            'accuracy_meters' => 5000,  // High accuracy radius to indicate no exact location
                            'address' => 'Location not available',
                            'status' => 'online',
                        ]);
                    }
                }
            }

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
