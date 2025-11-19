<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Clamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    /**
     * Display archives for admin (all archives)
     */
    public function adminIndex()
    {
        $archives = Archive::with('user', 'clamping')
            ->orderBy('archived_date', 'desc')
            ->paginate(20);

        return view('admin.archives', compact('archives'));
    }

    /**
     * Display archives for enforcer (only their own)
     */
    public function enforcerIndex()
    {
        $userId = Auth::id();
        $archives = Archive::where('user_id', $userId)
            ->with('clamping')
            ->orderBy('archived_date', 'desc')
            ->paginate(20);

        return view('dashboards.enforcer-archives', compact('archives'));
    }

    /**
     * Display archives for front desk (all released/cancelled clampings)
     */
    public function frontDeskIndex()
    {
        $archives = Archive::with('user', 'clamping')
            ->orderBy('archived_date', 'desc')
            ->paginate(20);

        return view('front-desk.archives', compact('archives'));
    }

    /**
     * Archive a clamping when it's released or cancelled
     */
    public function archive(Clamping $clamping, $status)
    {
        // Only archive if status is released or cancelled
        if (!in_array($status, ['released', 'cancelled'])) {
            return;
        }

        // Check if already archived
        $existing = Archive::where('clamping_id', $clamping->id)->first();
        if ($existing) {
            return; // Already archived
        }

        // Create archive record
        Archive::create([
            'clamping_id' => $clamping->id,
            'user_id' => $clamping->user_id,
            'ticket_no' => $clamping->ticket_no,
            'plate_no' => $clamping->plate_no,
            'vehicle_type' => $clamping->vehicle_type,
            'reason' => $clamping->reason,
            'location' => $clamping->location,
            'fine_amount' => $clamping->fine_amount,
            'archived_status' => $status,
            'archived_by' => Auth::id(),
        ]);
    }

    /**
     * Filter archives by various criteria
     */
    public function filter(Request $request)
    {
        $query = Archive::with('user', 'clamping');

        // Filter by status
        if ($request->filled('archived_status')) {
            $query->where('archived_status', $request->archived_status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('archived_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('archived_date', '<=', $request->end_date . ' 23:59:59');
        }

        // Filter by search (ticket no or plate no)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                  ->orWhere('plate_no', 'like', "%{$search}%");
            });
        }

        // For enforcers, only show their own archives
        if (Auth::user()->role_id == 2) {
            $query->where('user_id', Auth::id());
        }

        $archives = $query->orderBy('archived_date', 'desc')->paginate(20);

        return view('admin.archives', compact('archives'));
    }
}
