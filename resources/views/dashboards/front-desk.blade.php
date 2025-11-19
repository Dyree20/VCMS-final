@extends('layouts.front-desk')

@section('title', 'Front Desk Dashboard')

@section('content')
<div class="dashboard">
    <div class="wrapper">
        <!-- Top Header -->
        <h2>Hi, Front Desk Staff</h2>
        <p>Here's an overview of payment and violation inquiries</p>

        <div class="reports-container">
            <!-- Stats Cards -->
            <div class="reports">
                <div class="card">
                    <i class='bx bx-wallet'></i>
                    <h3>₱{{ number_format($todayCollections, 2) }}</h3>
                    <p>Today's Collections</p>
                    <span class="trend up">+22%</span>
                </div>
                <div class="card">
                    <i class='bx bx-money'></i>
                    <h3>₱{{ number_format($totalCollections, 2) }}</h3>
                    <p>Total Collections</p>
                    <span class="trend up">All Time</span>
                </div>
                <div class="card">
                    <i class='bx bx-hourglass'></i>
                    <h3>{{ $pendingPayments }}</h3>
                    <p>Pending Payments</p>
                    <span class="trend up">+5%</span>
                </div>
                <div class="card">
                    <i class='bx bx-check-circle'></i>
                    <h3>{{ $paidToday }}</h3>
                    <p>Paid Today</p>
                    <span class="trend up">+15%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="inquiries">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Recent Inquiries</h3>
            <div style="display: flex; gap: 10px;">
                <input type="text" id="searchInput" class="input-search" placeholder="Search by Ticket or Plate No..." style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; width: 250px;">
                <select id="statusFilter" class="select-filter" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="released">Released</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Ticket No.</th>
                        <th>Plate No.</th>
                        <th>Reason</th>
                        <th>Fine Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inquiriesTableBody">
                    @forelse($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->ticket_no }}</td>
                        <td>{{ $inquiry->plate_no }}</td>
                        <td>{{ Str::limit($inquiry->reason, 30) }}</td>
                        <td>₱{{ number_format($inquiry->fine_amount, 2) }}</td>
                        <td>
                            @php
                                $status = strtolower($inquiry->status ?? 'unknown');
                                $statusClass = match($status) {
                                    'paid' => 'active',
                                    'pending' => 'probation',
                                    'released' => 'inactive',
                                    'cancelled' => 'cancelled',
                                    default => '',
                                };
                            @endphp
                            <span class="status {{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td>{{ $inquiry->date_clamped ? \Carbon\Carbon::parse($inquiry->date_clamped)->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <a href="{{ route('front-desk.inquiry.show', $inquiry->id) }}" class="btn btn-sm btn-info" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">View</a>
                            @if($inquiry->status !== 'paid')
                            <form method="POST" action="{{ route('front-desk.inquiry.mark-paid', $inquiry->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" style="padding: 6px 12px; font-size: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Mark Paid</button>
                            </form>
                            @else
                            <span style="padding: 6px 12px; font-size: 12px; color: #999;">Already Paid</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #999;">No inquiries found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const baseUrl = window.location.origin;

    // Debounce function
    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func(...args), delay);
        };
    }

    // Search function
    const performSearch = debounce(function() {
        const searchQuery = searchInput.value;
        const statusValue = statusFilter.value;

        fetch(`{{ route('front-desk.search') }}?q=${encodeURIComponent(searchQuery)}&status=${encodeURIComponent(statusValue)}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('inquiriesTableBody');
                tbody.innerHTML = '';

                if (data.inquiries.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #999;">No results found</td></tr>`;
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
                    const markPaidAction = `${baseUrl}/front-desk/inquiry/${inquiry.id}/mark-paid`;
                    
                    const actionBtn = inquiry.status !== 'paid' 
                        ? `<form method="POST" action="${markPaidAction}" style="display: inline;"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn btn-sm btn-success" style="padding: 6px 12px; font-size: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Mark Paid</button></form>`
                        : '<span style="padding: 6px 12px; font-size: 12px; color: #999;">Already Paid</span>';

                    const row = `
                        <tr>
                            <td>${inquiry.ticket_no}</td>
                            <td>${inquiry.plate_no}</td>
                            <td>${inquiry.reason.substring(0, 30)}</td>
                            <td>₱${parseFloat(inquiry.fine_amount).toFixed(2)}</td>
                            <td><span class="status ${statusClass}">${statusText}</span></td>
                            <td>${date}</td>
                            <td>
                                <a href="${viewLink}" class="btn btn-sm btn-info" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">View</a>
                                ${actionBtn}
                            </td>
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
