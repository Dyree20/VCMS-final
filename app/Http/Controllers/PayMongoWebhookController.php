<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clamping;
use App\Models\Payee;
use Illuminate\Support\Facades\Log;

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Get webhook payload
        $payload = $request->getContent();
        $event = json_decode($payload);

        Log::info('🔔 PayMongo Webhook Received:', (array) $event);

        // Validate payload
        if (!isset($event->data->attributes->type)) {
            Log::warning('⚠️ Invalid webhook structure');
            return response()->json(['message' => 'Invalid webhook structure'], 400);
        }

        $type = $event->data->attributes->type;

        // Process successful payments
        if ($type === 'payment.paid' || $type === 'checkout_session.payment_paid') {
            $data = $event->data->attributes->data ?? $event->data->attributes;

            // Extract reference and amount (PayMongo sends in cents)
            $reference_id = $data->attributes->reference_number ?? $data->attributes->reference_id ?? null;
            $amount = isset($data->attributes->amount) ? $data->attributes->amount / 100 : 0;

            Log::info('✅ Webhook details:', [
                'reference_id' => $reference_id,
                'amount' => $amount,
            ]);

            if ($reference_id) {
                $clamping = Clamping::where('ticket_no', $reference_id)->first();

                if ($clamping) {
                    // Prevent duplicate payee entries
                    if (!Payee::where('ticket_no', $clamping->ticket_no)->exists()) {
                        Payee::create([
                            'ticket_no' => $clamping->ticket_no,
                            'name' => 'Online Payment', // ✅ no null
                            'contact_number' => null,
                            'payment_method' => 'online',
                            'amount_paid' => $amount > 0 ? $amount : $clamping->fine_amount,
                            'payment_date' => now(),
                        ]);

                        // Update clamping status
                        $clamping->update(['status' => 'Paid']);
                        Log::info("✅ Ticket {$clamping->ticket_no} marked as paid.");
                    }
                } else {
                    Log::warning("⚠️ No clamping found for reference: {$reference_id}");
                }
            }

            return response()->json(['message' => 'Payment processed successfully'], 200);
        }

        return response()->json(['message' => 'Event ignored'], 200);
    }
}
