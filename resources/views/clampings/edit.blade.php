@extends('layouts.app')

@section('title', 'Edit Clamping Status')

@section('content')
<div class="clamping-edit-container" style="max-width: 600px; margin: 40px auto; padding: 20px;">
    <div class="edit-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-size: 28px; color: #333;">Update Clamping Status</h2>
        <a href="{{ route('clampings') }}" class="btn btn-secondary" style="padding: 10px 20px; text-decoration: none;">Back to List</a>
    </div>

    <div class="edit-card" style="background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 30px;">
        <!-- Display Current Info -->
        <div class="current-info" style="background: white; padding: 20px; border-radius: 6px; border-left: 4px solid #007bff; margin-bottom: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Ticket No.</label>
                    <span style="font-size: 16px; font-weight: 600; color: #333;">{{ $clamping->ticket_no }}</span>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Plate No.</label>
                    <span style="font-size: 16px; font-weight: 600; color: #333;">{{ $clamping->plate_no }}</span>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Current Status</label>
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
                <div>
                    <label style="display: block; font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 5px;">Fine Amount</label>
                    <span style="font-size: 16px; font-weight: 600; color: #333;">₱{{ number_format($clamping->fine_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Status Update Form -->
        <form method="POST" action="{{ route('clampings.update', $clamping->id) }}" style="display: flex; flex-direction: column;">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 25px;">
                <label for="status" style="display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Update Status
                </label>
                <select name="status" id="status" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 16px; color: #333; background: white; cursor: pointer; transition: border-color 0.3s;">
                    <option value="">-- Select New Status --</option>
                    <option value="pending" {{ $clamping->status === 'pending' ? 'selected disabled' : '' }}>Pending</option>
                    <option value="paid">Paid</option>
                    <option value="released">Released</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                @error('status')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Status Information -->
            <div style="background: #e3f2fd; padding: 15px; border-radius: 6px; margin-bottom: 25px; border-left: 4px solid #2196f3;">
                <p style="margin: 0; font-size: 13px; color: #1565c0; line-height: 1.6;">
                    <strong>Status Guide:</strong><br>
                    • <strong>Pending:</strong> Initial status, awaiting payment or resolution<br>
                    • <strong>Paid:</strong> Fine has been paid by vehicle owner<br>
                    • <strong>Released:</strong> Vehicle clamp has been removed<br>
                    • <strong>Cancelled:</strong> Citation has been cancelled/withdrawn
                </p>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px 24px; background: #007bff; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 16px;">
                    <i class="fa-solid fa-check"></i> Update Status
                </button>
                <a href="{{ route('clampings.show', $clamping->id) }}" class="btn btn-secondary" style="flex: 1; padding: 12px 24px; background: #6c757d; color: white; border-radius: 6px; font-weight: 600; text-decoration: none; text-align: center; font-size: 16px;">
                    <i class="fa-solid fa-xmark" style="margin-right: 6px;"></i>Cancel
                </a>
            </div>
        </form>

        <!-- Release Button (Only if Status is Paid) -->
        @if(strtolower($clamping->status) === 'paid')
            <div style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #e0e0e0;">
                <div style="background: #c8e6c9; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #4caf50;">
                    <p style="margin: 0; font-size: 13px; color: #2e7d32; line-height: 1.6;">
                        <strong>Vehicle Released:</strong> The fine has been paid. Click the button below to release the vehicle clamp and archive this record.
                    </p>
                </div>
                <form method="POST" action="{{ route('clampings.release', $clamping->id) }}" style="display: flex;">
                    @csrf
                    <button type="submit" class="btn btn-release" style="flex: 1; padding: 14px 24px; background: #4caf50; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 16px; transition: background 0.3s ease;">
                        <i class="fa-solid fa-lock-open" style="margin-right: 6px;"></i>Release Vehicle
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@endsection
