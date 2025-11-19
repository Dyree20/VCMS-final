<?php

namespace App\Http\Controllers;

use App\Models\ParkingZone;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ParkingZoneController extends Controller
{
    public function index(): View
    {
        $zones = ParkingZone::with('createdBy')->latest()->paginate(15);
        return view('admin.zones.index', compact('zones'));
    }

    public function create(): View
    {
        return view('admin.zones.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:50|max:5000',
            'fine_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        ParkingZone::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('zones.index')->with('success', 'Parking zone created successfully');
    }

    public function show(ParkingZone $zone): View
    {
        $zone->load([
            'createdBy',
            'teams.members.role',
            'teams.members.status',
            'assignedEnforcers.role',
            'assignedEnforcers.status',
        ]);

        $clampingsInZone = $zone->clampings()->count();

        $availableTeams = Team::withCount('members')
            ->whereDoesntHave('parkingZones', function ($query) use ($zone) {
                $query->where('parking_zone_id', $zone->id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.zones.show', compact('zone', 'clampingsInZone', 'availableTeams'));
    }

    public function edit(ParkingZone $zone): View
    {
        return view('admin.zones.edit', compact('zone'));
    }

    public function update(ParkingZone $zone, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:50|max:5000',
            'fine_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $zone->update($validated);

        return redirect()->route('zones.index')->with('success', 'Parking zone updated successfully');
    }

    public function destroy(ParkingZone $zone): RedirectResponse
    {
        $zone->delete();
        return redirect()->route('zones.index')->with('success', 'Parking zone deleted successfully');
    }

    public function assignTeam(Request $request, ParkingZone $zone): RedirectResponse
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
        ]);

        if ($zone->teams()->where('team_id', $validated['team_id'])->exists()) {
            return back()->with('error', 'Team is already assigned to this zone.');
        }

        $zone->teams()->attach($validated['team_id'], [
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'Team assigned to parking zone successfully.');
    }

    public function removeTeam(ParkingZone $zone, Team $team): RedirectResponse
    {
        if (!$zone->teams()->where('team_id', $team->id)->exists()) {
            return back()->with('error', 'Team is not assigned to this zone.');
        }

        $zone->teams()->detach($team->id);

        return back()->with('success', 'Team removed from parking zone successfully.');
    }
}
