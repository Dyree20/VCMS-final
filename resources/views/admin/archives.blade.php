@extends('layouts.app')

@section('title', 'Archives')

@section('content')
<style>
    .archives-container {
        padding: 20px;
        background: #f5f7fa;
        min-height: 100vh;
    }

    .archives-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .archives-header h2 {
        margin: 0;
        color: #333;
        font-size: 28px;
        font-weight: 600;
    }

    .filter-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .filter-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .filter-title i {
        margin-right: 8px;
        color: #007bff;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-size: 13px;
        font-weight: 600;
        color: #555;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-group input,
    .filter-group select {
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        color: #333;
        background: white;
        transition: border-color 0.3s;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
    }

    .btn-filter {
        padding: 10px 24px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
    }

    .btn-reset {
        padding: 10px 24px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-reset:hover {
        background: #5a6268;
    }

    .archives-table-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .archives-table {
        width: 100%;
        border-collapse: collapse;
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
        border-bottom: 1px solid #f0f0f0;
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

    .archives-table .ticket-info {
        font-weight: 600;
        color: #333;
    }

    .archives-table .status-badge {
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

    .archives-table .user-info {
        color: #007bff;
        font-weight: 600;
    }

    .archives-table .timestamp {
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

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px;
        gap: 10px;
    }

    .pagination-wrapper a,
    .pagination-wrapper span {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        color: #007bff;
        font-size: 14px;
        transition: all 0.3s;
    }

    .pagination-wrapper a:hover {
        background: #007bff;
        color: white;
    }

    .pagination-wrapper .active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
</style>

<div class="archives-container">
    <!-- Header -->
    <div class="archives-header">
        <h2>📦 Archives</h2>
        <p style="color: #999; margin: 0;">View closed and released violations</p>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <h4 class="filter-title">
            <i class='bx bx-filter-alt'></i> Filter Archives
        </h4>
        
        <form action="{{ route('admin.archives.filter') }}" method="GET">
            <div class="filter-grid">
                <!-- Search -->
                <div class="filter-group">
                    <label for="search">Search Ticket/Plate</label>
                    <input type="text" id="search" name="search" placeholder="Enter ticket or plate number..." value="{{ request('search') }}">
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
                <a href="{{ route('admin.archives') }}" class="btn-reset">
                    <i class='bx bx-reset'></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Archives Table -->
    <div class="archives-table-wrapper">
        @if($archives->count() > 0)
            <table class="archives-table">
                <thead>
                    <tr>
                        <th>Ticket No</th>
                        <th>Plate No</th>
                        <th>Fine Amount</th>
                        <th>Status</th>
                        <th>Enforcer Name</th>
                        <th>Archived Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($archives as $archive)
                        <tr>
                            <td>
                                <span class="ticket-info">{{ $archive->ticket_no }}</span>
                            </td>
                            <td>{{ $archive->plate_no }}</td>
                            <td>₱{{ number_format($archive->fine_amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($archive->archived_status) }}">
                                    {{ ucfirst($archive->archived_status) }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #333;">
                                    {{ $archive->user ? ($archive->user->f_name . ' ' . $archive->user->l_name) : 'N/A' }}
                                </div>
                                <div style="font-size: 12px; color: #999; margin-top: 4px;">
                                    {{ $archive->user->enforcer_id ?? 'N/A' }}
                                </div>
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

            <!-- Pagination -->
            @if($archives->hasPages())
                <div class="pagination-wrapper">
                    {{ $archives->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="no-archives">
                <i class='bx bx-inbox'></i>
                <p>No archived violations found</p>
            </div>
        @endif
    </div>
</div>

@endsection
