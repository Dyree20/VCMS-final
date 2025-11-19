@extends('layouts.front-desk')

@section('title', 'Archives')

@section('content')
<style>
    .front-desk-archives {
        padding: 30px;
    }

    .archives-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 15px;
    }

    .archives-header h2 {
        margin: 0;
        color: #333;
        font-size: 24px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-section {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid #eee;
    }

    .filter-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #555;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .filter-group input,
    .filter-group select {
        padding: 8px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        color: #333;
        background: white;
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-filter {
        padding: 8px 16px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-filter:hover {
        background: #0056b3;
    }

    .btn-reset {
        padding: 8px 16px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-reset:hover {
        background: #5a6268;
    }

    .archives-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .archives-table thead {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        color: white;
    }

    .archives-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .archives-table tbody tr {
        border-bottom: 1px solid #eee;
        transition: background-color 0.2s;
    }

    .archives-table tbody tr:hover {
        background-color: #f9f9f9;
    }

    .archives-table td {
        padding: 14px 16px;
        font-size: 14px;
        color: #555;
    }

    .ticket-badge {
        background: #f0f0f0;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
        color: #333;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-released {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .officer-info {
        color: #007bff;
        font-weight: 600;
    }

    .timestamp {
        color: #999;
        font-size: 13px;
    }

    .no-archives {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .no-archives i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .no-archives p {
        margin: 0;
        font-size: 16px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
    }

    .pagination a:hover {
        background: #007bff;
        color: white;
    }

    .pagination .active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
</style>

<div class="front-desk-archives">
    <div class="archives-header">
        <h2>
            <i class='bx bx-archive'></i> Closed Violations Archives
        </h2>
        <p style="margin: 0; color: #999;">Total: {{ $archives->total() }} violations</p>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <h4 class="filter-title">
            <i class='bx bx-filter-alt'></i> Filter Archives
        </h4>
        
        <form action="{{ route('front-desk.archives') }}" method="GET">
            <div class="filter-grid">
                <!-- Search -->
                <div class="filter-group">
                    <label for="search">Search Ticket/Plate</label>
                    <input type="text" id="search" name="search" placeholder="Enter ticket or plate..." value="{{ request('search') }}">
                </div>

                <!-- Status Filter -->
                <div class="filter-group">
                    <label for="archived_status">Status</label>
                    <select id="archived_status" name="archived_status">
                        <option value="">All Status</option>
                        <option value="released" {{ request('archived_status') == 'released' ? 'selected' : '' }}>Released</option>
                        <option value="cancelled" {{ request('archived_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Start Date -->
                <div class="filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>

                <!-- End Date -->
                <div class="filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class='bx bx-search'></i> Apply Filters
                </button>
                <a href="{{ route('front-desk.archives') }}" class="btn-reset">
                    <i class='bx bx-reset'></i> Reset
                </a>
            </div>
        </form>
    </div>

    @if($archives->count() > 0)
        <table class="archives-table">
            <thead>
                <tr>
                    <th>Ticket No</th>
                    <th>Plate No</th>
                    <th>Fine Amount</th>
                    <th>Status</th>
                    <th>Issued By</th>
                    <th>Archived Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($archives as $archive)
                    <tr>
                        <td>
                            <span class="ticket-badge">{{ $archive->ticket_no }}</span>
                        </td>
                        <td>{{ $archive->plate_no }}</td>
                        <td><strong>₱{{ number_format($archive->fine_amount, 2) }}</strong></td>
                        <td>
                            <span class="status-badge status-{{ strtolower($archive->archived_status) }}">
                                {{ ucfirst($archive->archived_status) }}
                            </span>
                        </td>
                        <td>
                            @if($archive->user)
                                <span class="officer-info">
                                    {{ $archive->user->enforcer_id ?? 'N/A' }}
                                </span>
                                <div style="font-size: 12px; color: #999;">
                                    {{ $archive->user->f_name ?? '' }} {{ $archive->user->l_name ?? '' }}
                                </div>
                            @else
                                <span>—</span>
                            @endif
                        </td>
                        <td>
                            <span class="timestamp">
                                {{ $archive->archived_date->format('M d, Y H:i') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($archives->hasPages())
            <div class="pagination">
                {{ $archives->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="no-archives">
            <i class='bx bx-inbox'></i>
            <p>No archived violations yet</p>
        </div>
    @endif
</div>

@endsection
