<?php

namespace App\Http\Controllers;

use App\Models\Clamping;
use App\Models\Payee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontDeskController extends Controller
{
    public function index()
    {
        // Get today's collections
        $todayCollections = Payee::whereDate('created_at', today())->sum('amount');

        // Get total collections (all time)
        $totalCollections = Payee::sum('amount');

        // Get pending payments (exclude released - they're in archives)
        $pendingPayments = Clamping::where('status', '!=', 'released')
            ->where('status', '!=', 'paid')
            ->count();

        // Get paid today
        $paidToday = Payee::whereDate('created_at', today())->count();

        // Get recent inquiries (exclude released clampings)
        $inquiries = Clamping::where('status', '!=', 'released')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboards.front-desk', compact(
            'todayCollections',
            'totalCollections',
            'pendingPayments',
            'paidToday',
            'inquiries'
        ));
    }

    public function violations()
    {
        // Exclude archived clampings (released/cancelled) - they're in archives
        $clampings = Clamping::whereNotIn('status', ['released', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('front-desk.violations', compact('clampings'));
    }

    public function payments()
    {
        // Get today's collections
        $todayCollections = Payee::whereDate('created_at', today())->sum('amount');

        // Get total payments count
        $totalPayments = Payee::count();

        // Get pending amount (exclude archived - they're in archives)
        $pendingAmount = Clamping::whereNotIn('status', ['released', 'cancelled'])
            ->where('status', '!=', 'paid')
            ->sum('fine_amount');

        // Get all payments with clamping relationship (exclude archived)
        $payments = Payee::with('clamping')
            ->whereHas('clamping', function($q) {
                $q->whereNotIn('status', ['released', 'cancelled']);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('front-desk.payments', compact(
            'todayCollections',
            'totalPayments',
            'pendingAmount',
            'payments'
        ));
    }

    public function searchInquiries(Request $request)
    {
        $query = $request->get('q', '');
        $status = $request->get('status', '');

        $inquiries = Clamping::query();
        
        // Exclude archived clampings from search
        $inquiries->whereNotIn('status', ['released', 'cancelled']);

        if ($query) {
            $inquiries->where(function($q) use ($query) {
                $q->where('ticket_no', 'like', "%{$query}%")
                  ->orWhere('plate_no', 'like', "%{$query}%");
            });
        }

        if ($status) {
            $inquiries->where('status', $status);
        }

        $inquiries = $inquiries->orderBy('created_at', 'desc')->limit(20)->get();

        return response()->json(['inquiries' => $inquiries]);
    }

    public function showInquiry($id)
    {
        $clamping = Clamping::findOrFail($id);
        
        // Get payment info if exists
        $payment = Payee::where('clamping_id', $clamping->id)->first();

        return view('front-desk.inquiry-detail', compact('clamping', 'payment'));
    }

    public function markAsPaid($id)
    {
        $clamping = Clamping::findOrFail($id);
        
        // Check if violation is cancelled
        if ($clamping->status === 'cancelled') {
            return back()->with('error', 'Cannot mark a cancelled violation as paid.');
        }
        
        // Check if payment already exists
        $existingPayment = Payee::where('clamping_id', $clamping->id)->first();
        
        if (!$existingPayment) {
            Payee::create([
                'clamping_id' => $clamping->id,
                'ticket_no' => $clamping->ticket_no,
                'name' => 'Vehicle Owner', // Default name
                'amount' => $clamping->fine_amount,
                'amount_paid' => $clamping->fine_amount,
                'payment_method' => 'walk-in',
                'status' => 'completed',
            ]);
        }

        $clamping->status = 'paid';
        $clamping->save();

        return back()->with('success', 'Violation marked as paid successfully');
    }

    public function getStatistics()
    {
        return response()->json([
            'today_collections' => Payee::whereDate('created_at', today())->sum('amount'),
            'pending_payments' => Clamping::where('status', '!=', 'released')
                ->where('status', '!=', 'paid')
                ->count(),
            'paid_today' => Payee::whereDate('created_at', today())->count(),
        ]);
    }

    public function createPayment()
    {
        // Get unpaid clampings (exclude released - they're in archives)
        $unpaidClampings = Clamping::where('status', '!=', 'released')
            ->where('status', '!=', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('front-desk.payment-create', compact('unpaidClampings'));
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'ticket_no' => 'required|exists:clampings,ticket_no',
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        $clamping = Clamping::where('ticket_no', $validated['ticket_no'])->firstOrFail();

        // Check if violation is cancelled
        if ($clamping->status === 'cancelled') {
            return back()->with('error', 'Cannot record payment for a cancelled violation. Please contact admin.');
        }

        if ($clamping->status === 'paid') {
            return back()->with('error', 'This violation has already been paid.');
        }

        // Validate amount
        if ($validated['amount_paid'] < $clamping->fine_amount) {
            return back()->with('error', 'Amount paid cannot be less than the fine amount (₱' . number_format($clamping->fine_amount, 2) . ').');
        }

        // Create payment record
        Payee::create([
            'clamping_id' => $clamping->id,
            'ticket_no' => $validated['ticket_no'],
            'name' => $validated['name'],
            'contact_number' => $validated['contact_number'],
            'amount_paid' => $validated['amount_paid'],
            'amount' => $validated['amount_paid'],
            'payment_method' => 'walk-in',
            'status' => 'completed',
            'payment_date' => now(),
        ]);

        // Update clamping status
        $clamping->update(['status' => 'paid']);

        return back()->with('success', 'Payment recorded successfully! Ticket #' . $clamping->ticket_no . ' has been marked as paid.');
    }

    /**
     * Display archives for front desk (all released/cancelled clampings)
     */
    public function frontDeskArchives(Request $request)
    {
        $query = \App\Models\Archive::with('user', 'clamping');

        // Filter by status
        if ($request->filled('archived_status')) {
            $query->where('archived_status', $request->archived_status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('archived_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('archived_date', '<=', $request->end_date);
        }

        // Filter by search (ticket no or plate no)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                  ->orWhere('plate_no', 'like', "%{$search}%");
            });
        }

        $archives = $query->orderBy('archived_date', 'desc')->paginate(20);

        return view('front-desk.archives', compact('archives'));
    }
}
