<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $user->load(['role', 'status', 'details']);
        return view('admin.profile', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        $user->load(['role', 'status', 'details']);
        return view('admin.edit-profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'birthdate' => 'nullable|date',
        ]);

        // Update user
        $user->update([
            'f_name' => $validated['f_name'],
            'l_name' => $validated['l_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            if ($user->details && $user->details->photo) {
                Storage::delete('public/' . $user->details->photo);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
        } else {
            $path = $user->details ? $user->details->photo : null;
        }

        // Update or create user details
        UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            [
                'photo' => $path,
                'address' => $validated['address'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'birthdate' => $validated['birthdate'] ?? null,
            ]
        );

        // Determine redirect route based on user role
        $redirectRoute = match($user->role->name) {
            'Front Desk' => 'front-desk.profile',
            'Enforcer' => 'enforcer.profile',
            default => 'admin.profile',
        };

        return redirect()->route($redirectRoute)->with('success', 'Profile updated successfully!');
    }
}
