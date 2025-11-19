<?php

namespace App\Http\Controllers;

use App\Models\EnforcerLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnforcerTrackingController extends Controller
{
    public function index(): View
    {
        $enforcers = EnforcerLocation::with('user')
            ->recent()
            ->get()
            ->unique('user_id');

        return view('admin.tracking.index', compact('enforcers'));
    }

    public function trackEnforcer($enforcerId): View
    {
        $locations = EnforcerLocation::where('user_id', $enforcerId)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $enforcer = auth()->user()->find($enforcerId);

        return view('admin.tracking.show', compact('locations', 'enforcer'));
    }

    public function updateLocation(Request $request)
    {
        $user = auth()->user();

        // If request is just updating the tracking preference
        if ($request->has('location_tracking_enabled') && !$request->has('latitude')) {
            $user->update([
                'location_tracking_enabled' => $request->boolean('location_tracking_enabled'),
            ]);
            return response()->json(['success' => true, 'message' => 'Tracking preference updated']);
        }

        // Otherwise, it's a location submission
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string',
            'accuracy_meters' => 'nullable|integer',
            'status' => 'nullable|in:online,offline,on_break',
        ]);

        // Check if tracking is enabled
        if (!$user->location_tracking_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Location tracking is disabled. Enable it in your profile settings.'
            ], 403);
        }

        EnforcerLocation::create([
            'user_id' => $user->id,
            ...$validated,
            'status' => $validated['status'] ?? 'online',
        ]);

        return response()->json(['success' => true]);
    }

    public function getCurrentStatus()
    {
        $location = EnforcerLocation::where('user_id', auth()->id())
            ->latest()
            ->first();

        return response()->json($location ?? ['status' => 'offline']);
    }
}
