<?php

namespace App\Http\Controllers;

use App\Models\Clamping;
use App\Models\Payee;
use App\Models\Appeal;
use App\Models\User;
use Illuminate\View\View;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function dashboard(): View
    {
        $data = $this->getAnalyticsData();
        return view('admin.analytics.dashboard', $data);
    }

    private function getAnalyticsData()
    {
        $today = Carbon::now()->startOfDay();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        // Today Statistics
        $clampingsToday = Clamping::whereDate('created_at', $today)->count();
        $revenueToday = Payee::whereDate('created_at', $today)->sum('amount_paid');
        $releasedToday = Clamping::whereDate('created_at', $today)
            ->where('status', 'released')
            ->count();

        // This Week Statistics
        $clampingsWeek = Clamping::where('created_at', '>=', $thisWeek)->count();
        $revenueWeek = Payee::where('created_at', '>=', $thisWeek)->sum('amount_paid');
        $releasedWeek = Clamping::where('created_at', '>=', $thisWeek)
            ->where('status', 'released')
            ->count();

        // This Month Statistics
        $clampingsMonth = Clamping::where('created_at', '>=', $thisMonth)->count();
        $revenueMonth = Payee::where('created_at', '>=', $thisMonth)->sum('amount_paid');
        $releasedMonth = Clamping::where('created_at', '>=', $thisMonth)
            ->where('status', 'released')
            ->count();

        // This Year Statistics
        $clampingsYear = Clamping::where('created_at', '>=', $thisYear)->count();
        $revenueYear = Payee::where('created_at', '>=', $thisYear)->sum('amount_paid');

        // Status breakdown
        $statusBreakdown = Clamping::selectRaw('status, COUNT(*) as count')
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Top Enforcers
        $topEnforcers = Clamping::selectRaw('user_id, COUNT(*) as clampings_count, SUM(fine_amount) as total_fines')
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('user_id')
            ->orderByDesc('clampings_count')
            ->limit(10)
            ->with('user')
            ->get();

        // Daily trend (current month)
        $dailyData = Clamping::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(fine_amount) as revenue')
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Weekly trend (current year)
        $weeklyData = Clamping::selectRaw('WEEK(created_at) as week, COUNT(*) as count, SUM(fine_amount) as revenue')
            ->where('created_at', '>=', $thisYear)
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        // Monthly trend (all time)
        $monthlyData = Clamping::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(fine_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        // Appeal Statistics
        $appealStats = [
            'total' => Appeal::count(),
            'pending' => Appeal::pending()->count(),
            'under_review' => Appeal::underReview()->count(),
            'approved' => Appeal::where('status', 'approved')->count(),
            'rejected' => Appeal::where('status', 'rejected')->count(),
        ];

        return [
            'clampingsToday' => $clampingsToday,
            'clampingsWeek' => $clampingsWeek,
            'clampingsMonth' => $clampingsMonth,
            'clampingsYear' => $clampingsYear,
            'revenueToday' => $revenueToday,
            'revenueWeek' => $revenueWeek,
            'revenueMonth' => $revenueMonth,
            'revenueYear' => $revenueYear,
            'releasedToday' => $releasedToday,
            'releasedWeek' => $releasedWeek,
            'releasedMonth' => $releasedMonth,
            'statusBreakdown' => $statusBreakdown,
            'topEnforcers' => $topEnforcers,
            'dailyData' => $dailyData,
            'weeklyData' => $weeklyData,
            'monthlyData' => $monthlyData,
            'appealStats' => $appealStats,
        ];
    }
}
