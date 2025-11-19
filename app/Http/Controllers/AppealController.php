<?php

namespace App\Http\Controllers;

use App\Models\Appeal;
use App\Models\Clamping;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AppealController extends Controller
{
    public function index(): View
    {
        $appeals = Appeal::with(['clamping', 'user', 'resolvedBy'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Appeal::count(),
            'pending' => Appeal::pending()->count(),
            'under_review' => Appeal::underReview()->count(),
            'approved' => Appeal::where('status', 'approved')->count(),
            'rejected' => Appeal::where('status', 'rejected')->count(),
        ];

        return view('admin.appeals.index', compact('appeals', 'stats'));
    }

    public function show(Appeal $appeal): View
    {
        $appeal->load(['clamping', 'user', 'resolvedBy']);
        return view('admin.appeals.show', compact('appeal'));
    }

    public function create(Clamping $clamping): View
    {
        return view('appeals.create', compact('clamping'));
    }

    public function store(Request $request, Clamping $clamping): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:100',
            'description' => 'required|string|min:20',
        ]);

        $appeal = $clamping->appeals()->create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'reason' => $validated['reason'],
            'description' => $validated['description'],
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'appeal',
            'title' => 'Appeal Submitted',
            'message' => 'Your appeal has been submitted and is pending review.',
            'notifiable_type' => Appeal::class,
            'notifiable_id' => $appeal->id,
        ]);

        return redirect()->route('clampings.show', $clamping)->with('success', 'Appeal submitted successfully');
    }

    public function updateStatus(Appeal $appeal, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected',
            'resolution_notes' => 'nullable|string',
        ]);

        $appeal->update([
            'status' => $validated['status'],
            'resolution_notes' => $validated['resolution_notes'],
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        // Notify user of status change
        Notification::create([
            'user_id' => $appeal->user_id,
            'type' => 'appeal',
            'title' => 'Appeal Status Updated',
            'message' => "Your appeal has been {$validated['status']}.",
            'notifiable_type' => Appeal::class,
            'notifiable_id' => $appeal->id,
        ]);

        return back()->with('success', 'Appeal status updated');
    }

    public function destroy(Appeal $appeal): RedirectResponse
    {
        $appeal->delete();
        return back()->with('success', 'Appeal deleted');
    }
}
