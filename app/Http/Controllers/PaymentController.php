<?php

namespace App\Http\Controllers;

use App\Models\Payee;
use App\Models\Clamping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{   
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $totalCollected = DB::table('payees')
            ->whereDate('payment_date', $today)
            ->sum('amount_paid');

        $unpaidViolations = DB::table('clampings')
            ->where('status', 'pending')
            ->count();

        $ticketsToday = DB::table('clampings')
            ->whereNotIn('status', ['released', 'cancelled'])
            ->whereDate('date_clamped', $today)
            ->count();

        // Show all clampings (excluding released/cancelled) with their payment information
        // This allows admins to see all clampings and their payment status
        $query = Clamping::with(['user', 'payees'])
            ->whereNotIn('status', ['released', 'cancelled'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                  ->orWhere('plate_no', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'All Status') {
            $query->where('status', $request->status);
        }

        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('date_clamped', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date_clamped', '<=', $request->end_date);
        }

        $clampings = $query->paginate(20);

        return view('payment', compact(
            'totalCollected',
            'unpaidViolations',
            'ticketsToday',
            'clampings'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_no'      => 'required|exists:clampings,ticket_no',
            'name'           => 'required|string|max:255',
            'payment_method' => 'required|in:walk-in,online',
            'amount_paid'    => 'required|numeric|min:0',
        ]);

        $clamping = Clamping::where('ticket_no', $validated['ticket_no'])->first();

        if (!$clamping) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.'
            ], 404);
        }

        // Check if already paid (case-insensitive)
        if (strtolower($clamping->status) === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This ticket has already been paid.'
            ], 400); // 400 Bad Request
        }

        // Add clamping_id to validated data
        $validated['clamping_id'] = $clamping->id;
        $validated['amount'] = $validated['amount_paid'];
        $validated['status'] = 'completed';
        $validated['payment_date'] = now();

        $payee = Payee::create($validated);

        // Update clamping status to paid (lowercase for consistency) - release button will appear for manual release
        $clamping->update(['status' => 'paid']);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully. Please release the vehicle when ready.',
            'data'    => $payee,
        ], 201);
    }

     // ✅ Step 1: Create checkout session
    public function createCheckout($ticket_no)
    {
        $clamping = Clamping::where('ticket_no', $ticket_no)->firstOrFail();

        $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [[
                            'name' => 'Clamping Fine - ' . $clamping->plate_no,
                            'amount' => $clamping->fine_amount * 100, // PayMongo expects cents
                            'currency' => 'PHP',
                            'quantity' => 1,
                        ]],
                        'payment_method_types' => ['gcash'],
                        'success_url' => env('APP_URL') . '/payment/success/' . $clamping->id,
                        'cancel_url' => env('APP_URL') . '/payment/cancel',
                        'reference_number' => $clamping->ticket_no,
                    ],
                ],
            ]);

        if ($response->failed()) {
            return back()->with('error', 'Failed to create PayMongo checkout session.');
        }

        $checkout = $response->json();

        return redirect($checkout['data']['attributes']['checkout_url']);
    }


    // ✅ Step 2: Webhook endpoint (called by PayMongo)
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload);

        if (!isset($event->data->attributes->type)) {
            return response()->json(['message' => 'Invalid webhook'], 400);
        }

        $type = $event->data->attributes->type;

        if ($type === 'payment.paid' || $type === 'checkout_session.payment_paid') {
            $data = $event->data->attributes->data ?? $event->data->attributes;
            $reference_id = $data->attributes->reference_number ?? $data->attributes->reference_id ?? null;
            $amount = isset($data->attributes->amount) ? $data->attributes->amount / 100 : 0;

            $clamping = Clamping::where('ticket_no', $reference_id)->first();

            if ($clamping) {
                // Prevent duplicate payee entries
                if (!Payee::where('clamping_id', $clamping->id)->exists()) {
                    Payee::create([
                        'clamping_id' => $clamping->id,
                        'ticket_no' => $clamping->ticket_no,
                        'name' => 'Online Payment',
                        'payment_method' => 'online',
                        'amount_paid' => $amount > 0 ? $amount : $clamping->fine_amount,
                        'amount' => $amount > 0 ? $amount : $clamping->fine_amount,
                        'status' => 'completed',
                        'payment_date' => now(),
                    ]);

                    // Update clamping status to paid (lowercase for consistency) - release button will appear for manual release
                    $clamping->update(['status' => 'paid']);
                    Log::info('Payment processed via webhook. Release button will appear for manual release.', [
                        'ticket_no' => $clamping->ticket_no
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Webhook received']);
    }

    // ✅ Step 3: Show success and cancel pages
    public function success($id)
    {
        // If user is not authenticated, redirect to login
        if (!auth()->check()) {
            return redirect()->route('login.form')->with('error', 'Session expired. Please log in again.');
        }

        $clamping = Clamping::findOrFail($id);

        // Check if payment already exists to prevent duplicates
        $existingPayment = Payee::where('clamping_id', $clamping->id)->first();
        
        if (!$existingPayment) {
            // ✅ Insert into payees table
            Payee::create([
                'clamping_id' => $clamping->id,
                'ticket_no' => $clamping->ticket_no,
                'name' => 'Online Payment',
                'contact_number' => null,
                'payment_method' => 'online',
                'amount_paid' => $clamping->fine_amount,
                'amount' => $clamping->fine_amount,
                'status' => 'completed',
                'payment_date' => now(),
            ]);
        }

        // ✅ Update clamping status to paid (lowercase for consistency) - release button will appear for manual release
        if (strtolower($clamping->status) !== 'paid') {
            $clamping->update(['status' => 'paid']);
        }

        // Generate QR code
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->generate(url('/verify/' . $clamping->id));

        // ✅ Return to success view with updated info
        return view('payment.success', [
            'clamping' => $clamping->fresh(),
            'ticket_no' => $clamping->ticket_no,
            'status' => $clamping->fresh()->status,
            'qrCode' => $qrCode,
        ]);
    }


    public function cancel()
    {
        // If user is not authenticated, redirect to login
        if (!auth()->check()) {
            return redirect()->route('login.form')->with('error', 'Session expired. Please log in again.');
        }

        return view('payment.cancel');
    }

}
