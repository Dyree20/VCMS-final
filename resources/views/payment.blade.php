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

    <div class="filters">
        <input type="text" placeholder="Search by Plate No. / Ticket ID">
        <select>
            <option>All Status</option>
            <option>Paid</option>
            <option>Unpaid</option>
            <option>Pending</option>
        </select>
        <input type="date">
        <input type="date">
        <button>Filter</button>
    </div>
    
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
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->ticket_no }}</td>
                    <td>{{ $payment->clamping->plate_no ?? '—' }}</td>
                    <td>
                        <span style="font-weight: 600; color: #007bff;">
                            {{ $payment->clamping && $payment->clamping->user ? $payment->clamping->user->f_name . ' ' . $payment->clamping->user->l_name : 'N/A' }}
                        </span>
                        <div style="font-size: 12px; color: #999;">
                            {{ $payment->clamping && $payment->clamping->user ? $payment->clamping->user->enforcer_id : '—' }}
                        </div>
                    </td>
                    <td>{{ $payment->clamping->reason ?? '—' }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                    <td>
                        <span class="status paid">Paid</span>
                    </td>

                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('m/d/Y') }}</td>
                    <td>
                        <a href="{{ route('clampings.print', $payment->clamping->id) }}" class="btn-view-receipt" target="_blank">View Receipt</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding: 40px;">No payments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

</div>

@endsection

<script src="{{ asset('js/payment-filters.js') }}"></script>
