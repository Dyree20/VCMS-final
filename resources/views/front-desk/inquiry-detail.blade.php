@extends('layouts.front-desk')

@section('title', 'Inquiry Details')

@section('content')
<div style="max-width: 900px; margin: 40px auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-size: 28px; color: #333;">Inquiry Details</h2>
        <a href="{{ route('front-desk.dashboard') }}" class="btn btn-secondary" style="padding: 10px 20px; text-decoration: none; background: #6c757d; color: white; border-radius: 6px;">Back to Dashboard</a>
    </div>

    <div style="background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 30px;">
        <!-- Ticket Information -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #555; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Ticket Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Ticket No.</label>
                    <span style="font-size: 18px; font-weight: 600; color: #333;">{{ $clamping->ticket_no }}</span>
                </div>
                <div style="padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Date Clamped</label>
                    <span style="font-size: 18px; font-weight: 600; color: #333;">{{ \Carbon\Carbon::parse($clamping->date_clamped)->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Vehicle Information -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #555; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Vehicle Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Plate No.</label>
                    <span style="font-size: 18px; font-weight: 600; color: #333;">{{ $clamping->plate_no }}</span>
                </div>
                <div style="padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Vehicle Type</label>
                    <span style="font-size: 18px; font-weight: 600; color: #333;">{{ $clamping->vehicle_type ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Violation Details -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #555; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Violation Details</h3>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Reason</label>
                <p style="font-size: 16px; color: #333; line-height: 1.6; margin: 0;">{{ $clamping->reason }}</p>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Location</label>
                <p style="font-size: 16px; color: #333; line-height: 1.6; margin: 0;">{{ $clamping->location }}</p>
            </div>
        </div>

        <!-- Fine & Status -->
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #555; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Fine & Payment Status</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Fine Amount</label>
                    <span style="font-size: 18px; font-weight: 600; color: #333;">₱{{ number_format($clamping->fine_amount, 2) }}</span>
                </div>
                <div style="padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Violation Status</label>
                    @php
                        $status = strtolower($clamping->status ?? 'unknown');
                        $statusClass = match($status) {
                            'paid' => 'active',
                            'pending' => 'probation',
                            'released' => 'inactive',
                            'cancelled' => 'cancelled',
                            default => '',
                        };
                    @endphp
                    <span class="status {{ $statusClass }}" style="display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                        {{ ucfirst($status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        @if($payment)
        <div style="margin-bottom: 30px; background: #d4edda; padding: 20px; border-radius: 6px; border-left: 4px solid #28a745;">
            <h3 style="font-size: 16px; font-weight: 600; color: #155724; margin-top: 0; margin-bottom: 15px; text-transform: uppercase;">Payment Received</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; font-size: 12px; color: #155724; text-transform: uppercase; margin-bottom: 5px;">Amount Paid</label>
                    <span style="font-size: 18px; font-weight: 600; color: #155724;">₱{{ number_format($payment->amount, 2) }}</span>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #155724; text-transform: uppercase; margin-bottom: 5px;">Payment Date</label>
                    <span style="font-size: 18px; font-weight: 600; color: #155724;">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>
        @else
        <div style="margin-bottom: 30px; background: #fff3cd; padding: 20px; border-radius: 6px; border-left: 4px solid #ffc107;">
            <h3 style="font-size: 16px; font-weight: 600; color: #856404; margin-top: 0; margin-bottom: 10px;">⚠️ No Payment Recorded</h3>
            <p style="color: #856404; margin: 0;">This violation has not been marked as paid yet.</p>
        </div>
        @endif

        <!-- Photo -->
        @if($clamping->photo)
        <div style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #555; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Evidence Photo</h3>
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($clamping->photo) }}" alt="Clamping photo" style="max-width: 100%; height: auto; border-radius: 8px; border: 1px solid #ddd; display: block;">
        </div>
        @endif

        <!-- Action Buttons -->
        <div style="display: flex; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            @if($clamping->status === 'paid')
                <button disabled style="padding: 12px 24px; background: #ccc; color: #999; border: none; border-radius: 6px; font-weight: 600; cursor: not-allowed;">
                    ✓ Already Paid
                </button>
            @elseif($clamping->status === 'cancelled')
                <button disabled style="padding: 12px 24px; background: #ccc; color: #999; border: none; border-radius: 6px; font-weight: 600; cursor: not-allowed;" title="Cannot mark cancelled violations as paid">
                    ✗ Cancelled (Cannot Pay)
                </button>
            @else
                <form method="POST" action="{{ route('front-desk.inquiry.mark-paid', $clamping->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        ✓ Mark as Paid
                    </button>
                </form>
            @endif
            <a href="{{ route('front-desk.dashboard') }}" class="btn btn-secondary" style="padding: 12px 24px; background: #6c757d; color: white; border-radius: 6px; font-weight: 600; text-decoration: none;">
                ← Back
            </a>
        </div>
    </div>
</div>

@endsection
