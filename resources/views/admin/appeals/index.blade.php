@extends('layouts.app')

@section('title', 'Appeals Management')

@section('content')
<div class="appeals-container">
    <!-- Header -->
    <div class="appeals-header">
        <h1 class="appeals-title">
            <i class="fa-solid fa-rectangle-list"></i>
            Appeals Management
        </h1>
        <p class="appeals-subtitle">Review and manage violation appeals</p>
    </div>

    <!-- Stats Cards -->
    <div class="appeals-stats-grid">
        <div class="appeals-stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Appeals</div>
            </div>
        </div>
        <div class="appeals-stat-card pending">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="appeals-stat-card review">
            <div class="stat-icon">👁️</div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['under_review'] }}</div>
                <div class="stat-label">Under Review</div>
            </div>
        </div>
        <div class="appeals-stat-card approved">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['approved'] }}</div>
                <div class="stat-label">Approved</div>
            </div>
        </div>
    </div>

    <!-- Appeals Table -->
    <div class="appeals-card">
        <div class="appeals-table-header">
            <h2 class="appeals-section-title">All Appeals</h2>
        </div>

        <div class="appeals-table-wrapper">
            <table class="appeals-table">
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Appellant</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appeals as $appeal)
                    <tr>
                        <td class="ticket-cell">{{ $appeal->clamping->ticket_no ?? 'N/A' }}</td>
                        <td class="user-cell">
                            <div class="user-info">
                                <div class="user-avatar">{{ substr($appeal->user->f_name, 0, 1) }}{{ substr($appeal->user->l_name, 0, 1) }}</div>
                                <div>
                                    <div class="user-name">{{ $appeal->user->f_name }} {{ $appeal->user->l_name }}</div>
                                    <div class="user-email">{{ $appeal->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="reason-cell">{{ $appeal->reason }}</td>
                        <td class="status-cell">
                            <span class="status-badge {{ strtolower(str_replace('_', '-', $appeal->status)) }}">
                                {{ ucfirst(str_replace('_', ' ', $appeal->status)) }}
                            </span>
                        </td>
                        <td class="date-cell">{{ $appeal->created_at->format('M d, Y') }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('appeals.show', $appeal) }}" class="action-btn view-btn" title="View Details">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if($appeal->status !== 'resolved')
                                <form action="{{ route('appeals.destroy', $appeal) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this appeal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-cell">No appeals found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="appeals-pagination">
            {{ $appeals->links() }}
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('styles/appeals.css') }}">

@endsection
