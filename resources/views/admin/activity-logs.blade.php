@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<style>
    .activity-logs-container {
        padding: 20px;
        background: #f5f7fa;
        min-height: 100vh;
    }

    .logs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .logs-header h2 {
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

    .logs-table-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .logs-table {
        width: 100%;
        border-collapse: collapse;
    }

    .logs-table thead {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }

    .logs-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .logs-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }

    .logs-table tbody tr:hover {
        background-color: #f9f9f9;
    }

    .logs-table td {
        padding: 14px 16px;
        font-size: 14px;
        color: #555;
    }

    .logs-table .user-info {
        font-weight: 600;
        color: #007bff;
    }

    .logs-table .action-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .action-created {
        background: #d4edda;
        color: #155724;
    }

    .action-updated {
        background: #cce5ff;
        color: #004085;
    }

    .action-deleted {
        background: #f8d7da;
        color: #721c24;
    }

    .action-cancelled {
        background: #e2e3e5;
        color: #383d41;
    }

    .action-approved {
        background: #d1ecf1;
        color: #0c5460;
    }

    .action-rejected {
        background: #f5c6cb;
        color: #721c24;
    }

    .logs-table .timestamp {
        color: #999;
        font-size: 13px;
    }

    .no-logs {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .no-logs i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .no-logs p {
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

    .stats-bar {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #007bff;
    }

    .stat-card h4 {
        margin: 0 0 8px 0;
        font-size: 12px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .value {
        font-size: 24px;
        font-weight: 600;
        color: #333;
    }
</style>

<div class="activity-logs-container">
    <!-- Header -->
    <div class="logs-header">
        <h2>📋 Activity Logs</h2>
        <p style="color: #999; margin: 0;">Monitor all user actions and system activities</p>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <h4 class="filter-title">
            <i class='bx bx-filter-alt'></i> Filter Logs
        </h4>
        
        <form action="{{ route('activity-logs.filter') }}" method="GET">
            <div class="filter-grid">
                <!-- Search by Ticket/Action -->
                <div class="filter-group">
                    <label for="search">Search Ticket/Action</label>
                    <input type="text" id="search" name="search" placeholder="Enter ticket number or action..." value="{{ request('search') }}">
                </div>

                <!-- User Filter -->
                <div class="filter-group">
                    <label for="user_id">User</label>
                    <select id="user_id" name="user_id">
                        @foreach($users as $userId => $username)
                            <option value="{{ $userId }}" {{ request('user_id') == $userId ? 'selected' : '' }}>{{ $username }}</option>
                        @endforeach
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

                <!-- Action Filter -->
                <div class="filter-group">
                    <label for="action">Action Type</label>
                    <input type="text" id="action" name="action" placeholder="e.g., Created, Updated..." value="{{ request('action') }}">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class='bx bx-search'></i> Apply Filters
                </button>
                <a href="{{ route('activity-logs') }}" class="btn-reset">
                    <i class='bx bx-reset'></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="logs-table-wrapper">
        @if($logs->count() > 0)
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Ticket ID</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>
                                <span class="user-info">
                                    {{ $log->user->username ?? $log->username ?? 'Unknown User' }}
                                </span>
                                <div style="font-size: 12px; color: #999; margin-top: 2px;">
                                    {{ $log->user->f_name ?? '' }} {{ $log->user->l_name ?? '' }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $actionLower = strtolower($log->action);
                                    $actionClass = match(true) {
                                        str_contains($actionLower, 'created') => 'action-created',
                                        str_contains($actionLower, 'updated') => 'action-updated',
                                        str_contains($actionLower, 'deleted') => 'action-deleted',
                                        str_contains($actionLower, 'cancelled') => 'action-cancelled',
                                        str_contains($actionLower, 'approved') => 'action-approved',
                                        str_contains($actionLower, 'rejected') => 'action-rejected',
                                        default => 'action-updated',
                                    };
                                @endphp
                                <span class="action-badge {{ $actionClass }}">{{ $log->action }}</span>
                            </td>
                            <td>
                                <code style="background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-size: 12px;">
                                    {{ $log->clamping_id ?? '—' }}
                                </code>
                            </td>
                            <td>
                                <span class="timestamp">
                                    {{ $log->created_at->format('M d, Y H:i') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="pagination-wrapper">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="no-logs">
                <i class='bx bx-inbox'></i>
                <p>No activity logs found</p>
            </div>
        @endif
    </div>
</div>

@endsection
