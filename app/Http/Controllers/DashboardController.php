<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clamping;
use App\Models\Payee;
use App\Models\User;
use App\Models\Archive;
use App\Models\EnforcerLocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total clampings (excluding released)
        $totalClampings = Clamping::where('status', '!=', 'released')->count();
        
        // Total clampings from last 30 days for comparison
        $last30DaysClampings = Clamping::where('status', '!=', 'released')
            ->where('created_at', '>=', Carbon::now()->subDays(60))
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->count();
        
        // Current 30 days clampings
        $current30DaysClampings = Clamping::where('status', '!=', 'released')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        // Calculate trend for total clampings
        $clampingTrend = $this->calculateTrend($current30DaysClampings, $last30DaysClampings);

        // Active violations (pending status)
        $activeViolations = Clamping::where('status', 'pending')->count();
        
        // Active violations from last 30 days
        $last30DaysActive = Clamping::where('status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subDays(60))
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->count();
        
        // Current 30 days active violations
        $current30DaysActive = Clamping::where('status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        $activeTrend = $this->calculateTrend($current30DaysActive, $last30DaysActive);

        // Total collected fines
        $totalCollected = Payee::whereHas('clamping', function($q) {
            $q->where('status', '!=', 'released');
        })->sum('amount_paid');
        
        // Last 30 days collected
        $last30DaysCollected = Payee::whereHas('clamping', function($q) {
            $q->where('status', '!=', 'released');
        })
        ->where('created_at', '>=', Carbon::now()->subDays(60))
        ->where('created_at', '<', Carbon::now()->subDays(30))
        ->sum('amount_paid');
        
        // Current 30 days collected
        $current30DaysCollected = Payee::whereHas('clamping', function($q) {
            $q->where('status', '!=', 'released');
        })
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->sum('amount_paid');
        
        $collectedTrend = $this->calculateTrend($current30DaysCollected, $last30DaysCollected);

        // Get violations per day for the last 7 days (for chart)
        $violationsPerDay = Clamping::where('status', '!=', 'released')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Get enforcers with their stats and online status
        $enforcers = User::whereHas('role', function($q) {
            $q->where('name', 'Enforcer');
        })
        ->with(['role', 'status'])
        ->withCount(['clampings' => function($q) {
            $q->where('status', '!=', 'released');
        }])
        ->get()
        ->map(function($enforcer) {
            // Get latest location for this enforcer
            $latestLocation = EnforcerLocation::where('user_id', $enforcer->id)
                ->latest()
                ->first();
            
            // Check if enforcer is online (location updated in last 30 minutes)
            $enforcer->is_online = $latestLocation && $latestLocation->created_at->greaterThan(now()->subMinutes(30));
            $enforcer->online_status = $latestLocation ? $latestLocation->status : 'offline';
            $enforcer->last_seen = $latestLocation ? $latestLocation->created_at->diffForHumans() : 'Never';
            
            return $enforcer;
        });

        // Prepare chart data
        $chartLabels = [];
        $chartData = [];
        
        // Create a map of date => count for easy lookup
        $violationsMap = [];
        foreach ($violationsPerDay as $item) {
            $dateKey = Carbon::parse($item->date)->format('Y-m-d');
            $violationsMap[$dateKey] = (int)$item->count;
        }
        
        // Fill in all 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::now()->subDays($i)->format('M d');
            $chartData[] = $violationsMap[$date] ?? 0;
        }

        return view('dashboard', compact(
            'totalClampings',
            'clampingTrend',
            'activeViolations',
            'activeTrend',
            'totalCollected',
            'collectedTrend',
            'enforcers',
            'chartLabels',
            'chartData'
        ));
    }

    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? ['type' => 'up', 'value' => 100] : ['type' => 'neutral', 'value' => 0];
        }
        
        $percentage = (($current - $previous) / $previous) * 100;
        
        if ($percentage > 0) {
            return ['type' => 'up', 'value' => round(abs($percentage), 1)];
        } elseif ($percentage < 0) {
            return ['type' => 'down', 'value' => round(abs($percentage), 1)];
        } else {
            return ['type' => 'neutral', 'value' => 0];
        }
    }
}
