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
    public function index()
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

        // Only show payments for non-archived clampings
        $payments = Payee::with('clamping')
            ->whereHas('clamping', function($q) {
                $q->whereNotIn('status', ['released', 'cancelled']);
            })
            ->get();

        return view('payment', compact(
            'totalCollected',
            'unpaidViolations',
            'ticketsToday',
            'payments'
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

        if ($clamping->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This ticket has already been paid.'
            ], 400); // 400 Bad Request
        }

        $payee = Payee::create($validated);

        $clamping->update(['status' => 'Paid']);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully.',
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
                Payee::create([
                    'ticket_no' => $clamping->ticket_no,
                    'name' => 'Online Payment',
                    'payment_method' => 'online',
                    'amount_paid' => $amount,
                ]);

                $clamping->update(['status' => 'paid']);
            }
        }

        return response()->json(['message' => 'Webhook received']);
    }

    // ✅ Step 3: Show success and cancel pages
    public function success($id)
    {
        $clamping = Clamping::findOrFail($id);

        // ✅ Update clamping status
        $clamping->update(['status' => 'paid']);

        // ✅ Insert into payees table
        Payee::create([
            'ticket_no' => $clamping->ticket_no,
            'name' => 'Walk-in Payer', // or fetch from authenticated user
            'contact_number' => 'N/A',
            'payment_method' => 'online',
            'amount_paid' => $clamping->fine_amount,
            'payment_date' => now(),
        ]);

        // ✅ Return to success view with updated info
        return view('payment.success', [
            'ticket_no' => $clamping->ticket_no,
            'status' => $clamping->status, // Should now be 'paid'
        ]);
    }


    public function cancel()
    {
        return view('payment.cancel');
    }

}
