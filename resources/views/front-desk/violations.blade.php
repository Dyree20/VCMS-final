@extends('layouts.front-desk')

@section('title', 'Violations - Front Desk')

@section('content')
<div class="clamping-container">
    <h2 class="page-title">Violations</h2>

    <!-- Quick Actions -->
    <div class="actions-bar">
        <div class="search-filter">
            <input type="text" id="searchInput" class="input-text" placeholder="Search by Plate / Ticket No" style="flex: 1;">
            <select id="statusFilter" class="select-box">
                <option selected value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="released">Released</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Violations Table -->
    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ticket No.</th>
                    <th>Plate No.</th>
                    <th>Reason for Violation</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Fine Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="violationsTableBody">
                @forelse($clampings as $index => $clamping)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $clamping->ticket_no }}</td>
                        <td>{{ $clamping->plate_no }}</td>
                        <td>{{ Str::limit($clamping->reason, 30) }}</td>
                        <td>{{ $clamping->location }}</td>
                        <td>{{ $clamping->date_clamped ? \Carbon\Carbon::parse($clamping->date_clamped)->format('M d, Y') : 'N/A' }}</td>
                        <td>₱{{ number_format($clamping->fine_amount, 2) }}</td>
                        <td>
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
                            <span class="status {{ $statusClass }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('front-desk.inquiry.show', $clamping->id) }}" class="btn btn-info" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 30px; color: #999;">No violations found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
                const tbody = document.getElementById('violationsTableBody');
                tbody.innerHTML = '';

                if (data.inquiries.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 30px; color: #999;">No violations found</td></tr>`;
                    return;
                }

                data.inquiries.forEach((inquiry, index) => {
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
                            <td>${index + 1}</td>
                            <td>${inquiry.ticket_no}</td>
                            <td>${inquiry.plate_no}</td>
                            <td>${inquiry.reason.substring(0, 30)}</td>
                            <td>${inquiry.location}</td>
                            <td>${date}</td>
                            <td>₱${parseFloat(inquiry.fine_amount).toFixed(2)}</td>
                            <td><span class="status ${statusClass}">${statusText}</span></td>
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
