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
        $user->load(['role', 'status', 'details', 'devices']); // eager load devices for security tab
        
        // Return role-specific profile view - all roles use admin profile for consistent design
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
        
        $formType = $request->input('form_type', 'profile');

        if ($formType === 'login') {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email,' . $user->id,
                'username' => 'required|string|max:255|unique:users,username,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $updateData = [
                'email' => $validated['email'],
                'username' => $validated['username'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = $validated['password'];
            }

            $user->update($updateData);
        } else {
            $validated = $request->validate([
                'f_name' => 'required|string|max:255',
                'l_name' => 'required|string|max:255',
                'phone' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'address' => 'nullable|string',
                'nationality' => 'nullable|string|max:255',
                'gender' => 'nullable|string|in:Male,Female,Other',
                'birthdate' => 'nullable|date',
            ]);

            $user->update([
                'f_name' => $validated['f_name'],
                'l_name' => $validated['l_name'],
                'phone' => $validated['phone'] ?? $user->phone,
            ]);

            if ($request->hasFile('photo')) {
                if ($user->details && $user->details->photo) {
                    Storage::delete('public/' . $user->details->photo);
                }
                $path = $request->file('photo')->store('profile-photos', 'public');
            } else {
                $path = $user->details ? $user->details->photo : null;
            }

            UserDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'photo' => $path,
                    'address' => $validated['address'] ?? null,
                    'nationality' => $validated['nationality'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'birth_date' => $validated['birthdate'] ?? null,
                ]
            );
        }

        // Determine redirect route based on user role
        $redirectRoute = match($user->role->name) {
            'Front Desk' => 'front-desk.profile',
            'Enforcer' => 'enforcer.profile',
            default => 'admin.profile',
        };

        return redirect()->route($redirectRoute)->with('success', 'Profile updated successfully!');
    }
}
