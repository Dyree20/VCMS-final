<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clamping;
use App\Models\Payee;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;

class EnforcerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Exclude released clampings - they're in archives
        $totalClampings = Clamping::where('user_id', $user->id)
            ->where('status', '!=', 'released')
            ->count();
        $pendingCases = Clamping::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $totalPayments = Payee::whereHas('clamping', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', '!=', 'released');
        })->sum('amount_paid');

        $clampings = Clamping::where('user_id', $user->id)
            ->where('status', '!=', 'released')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboards.overview', compact('totalClampings', 'pendingCases', 'totalPayments', 'clampings'));
    }

    public function getSummary()
    {
        $user = auth()->user();
        
        return response()->json([
            'totalClampings' => Clamping::where('user_id', $user->id)
                ->where('status', '!=', 'released')
                ->count(),
            'pendingCases' => Clamping::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'totalPayments' => Payee::whereHas('clamping', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', '!=', 'released');
            })->sum('amount_paid'),
        ]);
    }

    public function profile()
    {
        $user = auth()->user();
        $user->load(['role', 'status', 'details']);
        return view('dashboards.profile', compact('user'));
    }

    public function editProfile()
    {
        $user = auth()->user();
        $user->load(['role', 'status', 'details']);
        return view('dashboards.edit-profile', compact('user'));
    }

    public function updateProfile(Request $request)
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

        return redirect()->route('enforcer.profile')->with('success', 'Profile updated successfully!');
    }

    public function updatePhoto(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->details && $user->details->photo) {
                Storage::delete('public/' . $user->details->photo);
            }
            $path = $request->file('photo')->store('profile-photos', 'public');
            
            // Update or create user details
            UserDetail::updateOrCreate(
                ['user_id' => $user->id],
                ['photo' => $path]
            );

            $photoUrl = asset('storage/' . $path);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully!',
                'photo_url' => $photoUrl
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No photo provided'
        ], 400);
    }

    public function transactionsHistory()
    {
        $transactions = Payee::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('dashboards.transactions-history', compact('transactions'));
    }

    public function notificationSettings()
    {
        return view('dashboards.notification-settings');
    }

    public function updateNotificationSettings(Request $request)
    {
        // Store notification settings in session or database
        // For now, store in session
        session([
            'email_clamping' => $request->has('email_clamping'),
            'email_payment' => $request->has('email_payment'),
            'email_status' => $request->has('email_status'),
            'app_alerts' => $request->has('app_alerts'),
        ]);

        return redirect()->route('notification.settings')->with('success', 'Notification settings updated!');
    }

    public function contactUs()
    {
        return view('dashboards.contact-us');
    }

    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        // Log contact message (could send email or store in database)
        \Log::info('Contact Form Submission', [
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('contact.us')->with('success', 'Your message has been sent! We will get back to you soon.');
    }

    public function helpFaqs()
    {
        return view('dashboards.help-faqs');
    }

    public function notifications()
    {
        $user = auth()->user();
        
        // Get enforcer's own clampings with status changes (exclude released)
        $myClampings = Clamping::where('user_id', $user->id)
            ->where('status', '!=', 'released')
            ->orderBy('updated_at', 'desc')
            ->take(50)
            ->get();

        // Get activity logs for enforcer's clampings
        $clampingIds = $myClampings->pluck('id');
        $activities = \App\Models\ActivityLog::whereIn('clamping_id', $clampingIds)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('clamping_id');

        return view('dashboards.notifications', compact('myClampings', 'activities'));
    }

    public function records()
    {
        // paginated records for enforcers (exclude released - they're in archives)
        $user = auth()->user();
        $records = Clamping::where('user_id', $user->id)
            ->where('status', '!=', 'released')
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        return view('dashboards.records', compact('records'));
    }


}
