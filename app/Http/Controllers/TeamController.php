<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of all teams (Admin only)
     */
    public function index()
    {
        $this->authorize('create', Team::class);
        
        $teams = Team::with(['creator', 'members'])->paginate(15);
        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new team (Admin only)
     */
    public function create()
    {
        $this->authorize('create', Team::class);
        // Get enforcers with Enforcer role and Approved status
        $enforcers = User::where('role_id', 2) // Enforcer role
                         ->whereHas('status', function($query) {
                             $query->where('status', 'Approved');
                         })
                         ->get();
        return view('admin.teams.create', compact('enforcers'));
    }

    /**
     * Store a newly created team (Admin only)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Team::class);
        
        $validated = $request->validate([
            'name' => 'required|string|unique:teams|max:255',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'enforcers' => 'nullable|array',
            'enforcers.*' => 'exists:users,id',
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Assign enforcers to team
        if (!empty($validated['enforcers'])) {
            foreach ($validated['enforcers'] as $enforcerId) {
                $team->members()->attach($enforcerId, [
                    'assigned_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('teams.show', $team)->with('success', 'Team created successfully');
    }

    /**
     * Display the specified team (Admin only)
     */
    public function show(Team $team)
    {
        $this->authorize('view', $team);
        $team->load(['creator', 'members' => function($query) {
            $query->with('role');
        }]);
        
        // Get available enforcers with Approved status
        $availableEnforcers = User::where('role_id', 2) // Enforcer role
                                  ->whereHas('status', function($query) {
                                      $query->where('status', 'Approved');
                                  })
                                  ->whereNotIn('id', $team->members->pluck('id'))
                                  ->get();
        
        return view('admin.teams.show', compact('team', 'availableEnforcers'));
    }

    /**
     * Show the form for editing the specified team (Admin only)
     */
    public function edit(Team $team)
    {
        $this->authorize('update', $team);
        $team->load(['members']);
        // Get enforcers with Approved status
        $enforcers = User::where('role_id', 2) // Enforcer role
                         ->whereHas('status', function($query) {
                             $query->where('status', 'Approved');
                         })
                         ->get();
        
        return view('admin.teams.edit', compact('team', 'enforcers'));
    }

    /**
     * Update the specified team (Admin only)
     */
    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        
        $validated = $request->validate([
            'name' => 'required|string|unique:teams,name,' . $team->id . '|max:255',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'enforcers' => 'nullable|array',
            'enforcers.*' => 'exists:users,id',
        ]);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Sync enforcers to team
        if (isset($validated['enforcers'])) {
            $syncData = [];
            foreach ($validated['enforcers'] as $enforcerId) {
                $syncData[$enforcerId] = ['assigned_by' => auth()->id()];
            }
            $team->members()->sync($syncData);
        } else {
            $team->members()->detach();
        }

        return redirect()->route('teams.show', $team)->with('success', 'Team updated successfully');
    }

    /**
     * Remove the specified team (Admin only)
     */
    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);
        
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted successfully');
    }

    /**
     * Add enforcer to team (Admin only)
     */
    public function addEnforcer(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        
        $validated = $request->validate([
            'enforcer_id' => 'required|exists:users,id',
        ]);

        // Check if enforcer is already in team
        if ($team->members()->where('user_id', $validated['enforcer_id'])->exists()) {
            return back()->with('error', 'Enforcer is already in this team');
        }

        $team->members()->attach($validated['enforcer_id'], [
            'assigned_by' => auth()->id(),
        ]);

        return back()->with('success', 'Enforcer added to team');
    }

    /**
     * Remove enforcer from team (Admin only)
     */
    public function removeEnforcer(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        
        $validated = $request->validate([
            'enforcer_id' => 'required|exists:users,id',
        ]);

        $team->members()->detach($validated['enforcer_id']);
        
        return back()->with('success', 'Enforcer removed from team');
    }
}
