@extends('layouts.app')

@section('title', 'View Clamping Record')

@section('content')
<div class="clamping-detail-container" style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <div class="detail-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-size: 28px; color: #333;">Clamping Record Details</h2>
        <a href="{{ route('clampings') }}" class="btn btn-secondary" style="padding: 10px 20px; text-decoration: none;">Back to List</a>
    </div>

    <div class="detail-card" style="background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 30px;">
        <!-- Ticket Information -->
        <div class="detail-section" style="margin-bottom: 30px;">
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
        <div class="detail-section" style="margin-bottom: 30px;">
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
        <div class="detail-section" style="margin-bottom: 30px;">
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
        <div class="detail-section" style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #555; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Fine & Status</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Fine Amount</label>
                    <span style="font-size: 18px; font-weight: 600; color: #333;">₱{{ number_format($clamping->fine_amount, 2) }}</span>
                </div>
                <div style="padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Status</label>
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

        <!-- Photo -->
        <div class="detail-section" style="margin-bottom: 30px;">
            <h3 style="font-size: 16px; font-weight: 600; color: #555; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Evidence Photo</h3>
            @if($clamping->photo)
                <img src="{{ asset('storage/' . $clamping->photo) }}" alt="Clamping photo" style="max-width: 100%; height: auto; border-radius: 8px; border: 1px solid #ddd; display: block;">
            @else
                <div style="background: #f5f5f5; border: 2px dashed #ddd; border-radius: 8px; padding: 40px; text-align: center;">
                    <p style="color: #999; font-size: 16px; margin: 0;">📷 No evidence photo available</p>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="detail-actions" style="display: flex; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; flex-wrap: wrap;">
            @if(strtolower($clamping->status) === 'paid')
                <!-- For PAID status: Show View and Release buttons only -->
                <button onclick="window.location.href='{{ route('clampings.show', $clamping->id) }}'" class="btn btn-primary" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    👁️ View
                </button>
                <form method="POST" action="{{ route('clampings.release', $clamping->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        🔓 Release
                    </button>
                </form>
            @else
                <!-- For other statuses: Show Edit, Cancel, Delete -->
                <a href="{{ route('clampings.edit', $clamping->id) }}" class="btn btn-primary" style="padding: 12px 24px; text-decoration: none; background: #007bff; color: white; border-radius: 6px; font-weight: 600;">
                    ✏️ Edit Status
                </a>
                <form method="POST" action="{{ route('clampings.cancel', $clamping->id) }}" style="display: inline;">
                    @csrf
                    @if($clamping->status !== 'cancelled')
                    <button type="submit" class="btn btn-warning" style="padding: 12px 24px; background: #ffc107; color: #333; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        ⛔ Cancel
                    </button>
                    @else
                    <button type="submit" class="btn btn-warning" disabled style="padding: 12px 24px; background: #ccc; color: #999; border: none; border-radius: 6px; font-weight: 600; cursor: not-allowed;">
                        Already Cancelled
                    </button>
                    @endif
                </form>
                <form method="POST" action="{{ route('clampings.destroy', $clamping->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this clamping record? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding: 12px 24px; background: #dc3545; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        🗑️ Delete
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection
