<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserStatusChanged;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with([
            'role',
            'status',
            'details',
            'teams.parkingZones',
            'parkingZone',
        ])->get();

        $totalUsers = $users->count();
        $activeUsers = $users->filter(function($user) {
            return strtolower($user->status->status ?? '') === 'approved';
        })->count();
        $pendingUsers = $users->filter(function($user) {
            return strtolower($user->status->status ?? '') === 'pending';
        })->count();
        $inactiveUsers = $users->filter(function($user) {
            return strtolower($user->status->status ?? '') === 'suspended';
        })->count();

        return view('users', compact('users', 'totalUsers', 'activeUsers', 'pendingUsers', 'inactiveUsers'));

    }

    /**
     * Fetch users for AJAX (optional, for filtering/search)
     */
    public function fetchUsers(Request $request)
    {
        $users = User::with([
            'role',
            'status',
            'details',
            'teams.parkingZones',
            'parkingZone',
        ])->get();
        return response()->json($users);
    }

    /**
     * Approve a pending user (set status to 'Approved')
     */
    public function approve(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $approvedStatus = \App\Models\UserStatus::where('status', 'Approved')->firstOrFail();
        
        $user->status_id = $approvedStatus->id;
        $user->save();

        // Send email notification
        Mail::to($user->email)->send(new UserStatusChanged($user, 'approved'));

        return response()->json(['success' => true, 'message' => 'User approved', 'status' => 'Approved']);
    }

    /**
     * Reject a user (set status to 'Rejected')
     */
    public function reject(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $rejectedStatus = \App\Models\UserStatus::where('status', 'Rejected')->firstOrFail();
        
        $user->status_id = $rejectedStatus->id;
        $user->save();

        // Send email notification
        Mail::to($user->email)->send(new UserStatusChanged($user, 'rejected'));

        return response()->json(['success' => true, 'message' => 'User rejected', 'status' => 'Rejected']);
    }

    /**
     * Show detailed user info (admin view)
     */
    public function show(Request $request, $id)
    {
        $user = User::with(['role', 'status', 'details'])->findOrFail($id);

        return view('users.show', compact('user'));
    }

    /**
     * Update assigned area for a user (enforcer)
     */
    public function updateAssignedArea(Request $request, $id)
    {
        $request->validate([
            'assigned_area' => 'nullable|string|max:255',
        ]);

        $user = User::with('role')->findOrFail($id);
        
        // Only allow assigning areas to enforcers
        if (strtolower($user->role->name ?? '') !== 'enforcer') {
            return response()->json([
                'success' => false,
                'message' => 'Area assignment is only available for enforcers.'
            ], 400);
        }

        $user->assigned_area = $request->assigned_area;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Assigned area updated successfully',
            'assigned_area' => $user->assigned_area
        ]);
    }

    /**
     * Toggle account status (activate/deactivate)
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:approved,suspended',
            ]);

            $user = User::findOrFail($id);
            $statusName = ucfirst($request->status);
            $status = \App\Models\UserStatus::where('status', $statusName)->first();
            
            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => "Status '{$statusName}' not found in the system."
                ], 404);
            }
            
            $user->status_id = $status->id;
            $user->save();

            // Send email notification (wrap in try-catch to prevent email failures from breaking the request)
            try {
                $action = $request->status === 'approved' ? 'activated' : 'deactivated';
                Mail::to($user->email)->send(new UserStatusChanged($user, $action));
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                \Log::warning('Failed to send status change email to user ' . $user->id . ': ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Account status updated successfully',
                'status' => $statusName
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error toggling user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating account status: ' . $e->getMessage()
            ], 500);
        }
    }
}
