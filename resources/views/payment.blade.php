@extends('layouts.app')

@section('title', 'Clamping Dashboard')

@section('content')

<div class="payments-container">
    <div class="summary-cards">
        <div class="card">
            <h4>Total Collected Today</h4>
            <p>₱{{ number_format($totalCollected, 2) }}</p>
        </div>
        <div class="card">
            <h4>Unpaid Violations</h4>
            <p>{{ $unpaidViolations }}</p>
        </div>
        <div class="card">
            <h4>Total Tickets Issued Today</h4>
            <p>{{ $ticketsToday }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('payments') }}" class="filters">
        <input type="text" name="search" placeholder="Search by Plate No. / Ticket ID" value="{{ request('search') }}">
        <select name="status">
            <option value="All Status" {{ request('status') == 'All Status' || !request('status') ? 'selected' : '' }}>All Status</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        </select>
        <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="From Date">
        <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="To Date">
        <button type="submit">Filter</button>
    </form>
    
    <div class="payment_table_wrapper" style="margin-top: 30px; min-height: 500px;">
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Ticket No.</th>
                    <th>Plate No.</th>
                    <th>Attending Officer</th>
                    <th>Violation</th>
                    <th>Payment Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date Paid</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clampings as $clamping)
                @php
                    $payment = $clamping->payees->first(); // Get the first payment record if exists
                    $clampingStatus = strtolower($clamping->status ?? 'unknown');
                    $statusClass = match($clampingStatus) {
                        'paid' => 'paid',
                        'pending' => 'pending',
                        default => 'unknown'
                    };
                @endphp
                <tr>
                    <td>{{ $clamping->ticket_no }}</td>
                    <td>{{ $clamping->plate_no ?? '—' }}</td>
                    <td>
                        <span style="font-weight: 600; color: #007bff;">
                            {{ $clamping->user ? $clamping->user->f_name . ' ' . $clamping->user->l_name : 'N/A' }}
                        </span>
                        <div style="font-size: 12px; color: #999;">
                            {{ $clamping->user && $clamping->user->enforcer_id ? $clamping->user->enforcer_id : '—' }}
                        </div>
                    </td>
                    <td>{{ $clamping->reason ?? '—' }}</td>
                    <td>{{ $payment ? ucfirst($payment->payment_method ?? 'N/A') : '—' }}</td>
                    <td>₱{{ number_format($clamping->fine_amount ?? 0, 2) }}</td>
                    <td>
                        <span class="status {{ $statusClass }}">{{ ucfirst($clampingStatus) }}</span>
                    </td>
                    <td>
                        @if($payment && $payment->payment_date)
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}
                        @elseif($payment && $payment->created_at)
                            {{ \Carbon\Carbon::parse($payment->created_at)->format('m/d/Y') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($payment)
                            <a href="{{ route('clampings.print', $clamping->id) }}" class="btn-view-receipt" target="_blank">View Receipt</a>
                        @else
                            <a href="{{ route('clampings.show', $clamping->id) }}" class="btn-view-receipt" style="background: #6c757d;">View Details</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding: 40px;">No clampings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($clampings->hasPages())
    <div style="display: flex; justify-content: center; margin-top: 30px;">
        {{ $clampings->links() }}
    </div>
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

</div>

@endsection

<script src="{{ asset('js/payment-filters.js') }}"></script>
