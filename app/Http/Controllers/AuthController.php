<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserStatus;
use App\Models\EnforcerLocation;
use App\Mail\PasswordResetEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login'); // your Blade file path
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = UserStatus::where('status', 'Pending')->firstOrFail();

        $user = User::create([
            'f_name' => $validated['f_name'],
            'l_name' => $validated['l_name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role_id' => null,
            'status_id' => $status->id,
        ]);

        // Generate enforcer_id based on role
        $user->enforcer_id = $user->generateEnforcerId();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Registration successful!',
            'user' => $user
        ], 201);
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $login_type => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $status = strtolower($user->status->status ?? '');
            
            // Allow pending users to log in with limited access
            if ($status === 'pending') {
                // Pending users can log in - they'll have limited access via middleware
                // Log device information for this login
                \App\Http\Controllers\DeviceManagerController::logDevice($request, $user);
                
                // Redirect pending users to their profile only
                return redirect()->route($user->role_id === 2 ? 'enforcer.profile' : 'admin.profile')
                    ->with('warning', 'Your account is pending approval. You can only access your profile.');
            }
            
            // Block suspended and rejected users
            if (in_array($status, ['suspended', 'rejected'])) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Your account is ' . $status . '. Please contact the administrator.'
                ])->onlyInput('login');
            }

            // Log device information for this login
            \App\Http\Controllers\DeviceManagerController::logDevice($request, $user);

            if ($user->role_id === 1) {
                return redirect()->intended('/dashboard');
            } elseif ($user->role_id === 2) {
                return redirect()->intended('/enforcer/dashboard');
            } elseif ($user->role_id === 3) {
                return redirect()->intended('/front-desk/dashboard');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors([
            'login' => 'Invalid username/email or password.',
        ])->onlyInput('login');
    }


    // Show forgot password form
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Forgot password - send reset link
    public function forgotPasswordEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email not found in our system.',
            ])->onlyInput('email');
        }

        // Generate a reset token and store in database
        $resetToken = hash_hmac('sha256', uniqid() . $user->email . time(), config('app.key'));
        $user->reset_token = $resetToken;
        $user->reset_token_expires = now()->addHour();
        $user->save();

        // Send reset link via email
        $resetUrl = route('password.reset.form', ['token' => $resetToken, 'email' => $user->email]);
        
        try {
            Mail::send(new PasswordResetEmail($resetUrl, $user->f_name, $user->email));
            return back()->with('success', 'Password reset link has been sent to your email. Please check your inbox.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Failed to send reset email. Please try again later.'
            ])->onlyInput('email');
        }
    }

    // Show password reset form
    public function showResetForm($token, $email)
    {
        $user = User::where('email', $email)
                    ->where('reset_token', $token)
                    ->where('reset_token_expires', '>', now())
                    ->first();

        if (!$user) {
            return redirect('/login')->withErrors([
                'login' => 'This password reset link has expired or is invalid.'
            ]);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    // Handle password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)
                    ->where('reset_token', $request->token)
                    ->where('reset_token_expires', '>', now())
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'password' => 'This password reset link has expired or is invalid.'
            ]);
        }

        // Update password and clear reset token
        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->reset_token_expires = null;
        $user->save();

        return redirect('/login')->with('success', 'Your password has been reset successfully. Please log in with your new password.');
    }

    // Optional: logout
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Set enforcer status to offline when they log out
        if ($user) {
            // Load role relationship to check if enforcer
            $user->load('role');
            $roleName = strtolower($user->role->name ?? '');
            
            if ($roleName === 'enforcer') {
                // Only set offline if this is the last active device for the user
                $activeDevicesCount = $user->devices()->where('is_active', true)->count();

                if ($activeDevicesCount <= 1) {
                    // Update all recent locations with offline status
                    EnforcerLocation::where('user_id', $user->id)
                        ->where('created_at', '>=', now()->subHours(1))
                        ->update(['status' => 'offline']);

                    // Create a new offline location record to ensure they show as offline
                    $lastLocation = EnforcerLocation::where('user_id', $user->id)
                        ->latest()
                        ->first();

                    if ($lastLocation) {
                        EnforcerLocation::create([
                            'user_id' => $user->id,
                            'latitude' => $lastLocation->latitude,
                            'longitude' => $lastLocation->longitude,
                            'accuracy_meters' => $lastLocation->accuracy_meters,
                            'address' => $lastLocation->address,
                            'status' => 'offline',
                        ]);
                    } else {
                        EnforcerLocation::create([
                            'user_id' => $user->id,
                            'latitude' => 14.5995,
                            'longitude' => 121.0012,
                            'accuracy_meters' => 5000,
                            'address' => 'Location not available',
                            'status' => 'offline',
                        ]);
                    }
                }
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

}

