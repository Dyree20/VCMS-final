<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get all users for the dropdown filter
        $users = User::all()
            ->pluck('username', 'id')
            ->prepend('All Users', '');

        return view('admin.activity-logs', compact('logs', 'users'));
    }

    public function filter(Request $request)
    {
        $query = ActivityLog::query();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by clamping ticket
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('clamping_id', 'like', "%{$request->search}%")
                  ->orWhere('action', 'like', "%{$request->search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get all users for the dropdown filter
        $users = User::all()
            ->pluck('username', 'id')
            ->prepend('All Users', '');

        return view('admin.activity-logs', compact('logs', 'users'));
    }
}
