@extends('layouts.front-desk')

@section('title', 'Payments - Front Desk')

@section('content')
<div style="max-width: 1400px; margin: 20px auto; padding: 20px;">
    <h2 style="font-size: 28px; font-weight: 600; margin-bottom: 30px; color: #333;">Payment Records</h2>

    <!-- Payment Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0; color: #999; font-size: 12px; text-transform: uppercase;">Today's Collections</p>
            <h3 style="margin: 10px 0 0 0; font-size: 24px; color: #28a745;">₱{{ number_format($todayCollections, 2) }}</h3>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0; color: #999; font-size: 12px; text-transform: uppercase;">Total Payments</p>
            <h3 style="margin: 10px 0 0 0; font-size: 24px; color: #007bff;">{{ $totalPayments }}</h3>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="margin: 0; color: #999; font-size: 12px; text-transform: uppercase;">Pending Amount</p>
            <h3 style="margin: 10px 0 0 0; font-size: 24px; color: #ffc107;">₱{{ number_format($pendingAmount, 2) }}</h3>
        </div>
    </div>

    <!-- Filters -->
    <div style="display: flex; gap: 15px; margin-bottom: 20px;">
        <input type="text" id="searchInput" placeholder="Search by Ticket or Plate No..." style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px;">
        <select id="statusFilter" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px;">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="released">Released</option>
        </select>
    </div>

    <!-- Payments Table -->
    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Ticket No.</th>
                    <th>Plate No.</th>
                    <th>Fine Amount</th>
                    <th>Paid Amount</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="paymentsTableBody">
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->ticket_no }}</td>
                        <td>{{ $payment->clamping->plate_no ?? 'N/A' }}</td>
                        <td>₱{{ number_format($payment->clamping->fine_amount ?? 0, 2) }}</td>
                        <td>₱{{ number_format($payment->amount_paid ?? $payment->amount ?? 0, 2) }}</td>
                        <td>
                            @php
                                $paymentStatus = strtolower($payment->clamping->status ?? 'unknown');
                                $statusClass = match($paymentStatus) {
                                    'paid' => 'active',
                                    'pending' => 'probation',
                                    'released' => 'inactive',
                                    default => '',
                                };
                            @endphp
                            <span class="status {{ $statusClass }}">{{ ucfirst($paymentStatus) }}</span>
                        </td>
                        <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('front-desk.inquiry.show', $payment->clamping_id) }}" class="btn btn-info" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #999;">No payment records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Walk-in Payment Button -->
    <div style="margin-top: 25px; display: flex; justify-content: center;">
        <a href="{{ route('front-desk.payment.create') }}" style="padding: 14px 40px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; text-decoration: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3); display: inline-block;">
            + Record Walk-in Payment
        </a>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const baseUrl = window.location.origin;

    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func(...args), delay);
        };
    }

    const performSearch = debounce(function() {
        const searchQuery = searchInput.value;
        const statusValue = statusFilter.value;

        fetch(`{{ route('front-desk.search') }}?q=${encodeURIComponent(searchQuery)}&status=${encodeURIComponent(statusValue)}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('paymentsTableBody');
                tbody.innerHTML = '';

                if (data.inquiries.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #999;">No payment records found</td></tr>`;
                    return;
                }

                data.inquiries.forEach(inquiry => {
                    const statusClass = {
                        'paid': 'active',
                        'pending': 'probation',
                        'released': 'inactive',
                        'cancelled': 'cancelled',
                    }[inquiry.status] || '';

                    const statusText = inquiry.status.charAt(0).toUpperCase() + inquiry.status.slice(1);
                    const date = new Date(inquiry.date_clamped).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });
                    const viewLink = `${baseUrl}/front-desk/inquiry/${inquiry.id}`;

                    const row = `
                        <tr>
                            <td>${inquiry.ticket_no}</td>
                            <td>${inquiry.plate_no}</td>
                            <td>₱${parseFloat(inquiry.fine_amount).toFixed(2)}</td>
                            <td>₱${parseFloat(inquiry.fine_amount).toFixed(2)}</td>
                            <td><span class="status ${statusClass}">${statusText}</span></td>
                            <td>${date}</td>
                            <td><a href="${viewLink}" class="btn btn-info" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">View</a></td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            })
            .catch(error => console.error('Error:', error));
    }, 300);

    searchInput.addEventListener('keyup', performSearch);
    statusFilter.addEventListener('change', performSearch);
</script>

@endsection
