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
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        // Clamping Statistics
        $clampingsToday = Clamping::whereDate('created_at', $today)->count();
        $clampingsMonth = Clamping::where('created_at', '>=', $thisMonth)->count();
        $clampingsYear = Clamping::where('created_at', '>=', $thisYear)->count();

        // Revenue Statistics
        $revenueToday = Payee::whereDate('created_at', $today)->sum('amount_paid');
        $revenueMonth = Payee::where('created_at', '>=', $thisMonth)->sum('amount_paid');
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

        // Monthly trend
        $monthlyData = Clamping::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(fine_amount) as revenue')
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('date')
            ->orderBy('date')
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
            'clampingsMonth' => $clampingsMonth,
            'clampingsYear' => $clampingsYear,
            'revenueToday' => $revenueToday,
            'revenueMonth' => $revenueMonth,
            'revenueYear' => $revenueYear,
            'statusBreakdown' => $statusBreakdown,
            'topEnforcers' => $topEnforcers,
            'monthlyData' => $monthlyData,
            'appealStats' => $appealStats,
        ];
    }
}
