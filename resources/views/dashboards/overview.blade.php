@extends('dashboards.enforcer')

@section('title', 'Overview')

@section('content')
@php
    $user = auth()->user();
    $user->load('details');
    $profilePhoto = $user->details && $user->details->photo 
        ? asset('storage/' . $user->details->photo) 
        : asset('images/default-avatar.png');
@endphp
<header>
    <h2>Overview</h2>
    <a href="{{ route('enforcer.profile') }}" class="profile-link" title="View Profile">
        <img src="{{ $profilePhoto }}" alt="Profile" class="profile-picture" id="headerProfilePic">
    </a>
</header>

<!-- GPS Location Tracking (if location tracking is enabled) -->
@if($user->location_tracking_enabled)
    @include('components.gps-tracker')
@endif

<section class="summary">
    <div class="summary-card big">
        <h4>Total Clampings</h4>
        <h2>{{ $totalClampings ?? 0 }}</h2>
        <p><i class="fa-solid fa-car-burst"></i> Updated Today</p>
    </div>

    <div class="column">
        <div class="summary-card small">
            <h4>Pending Cases</h4>
            <p>{{ $pendingCases ?? 0 }}</p>
            <i class="fa-solid fa-clock"></i>
        </div>
        <div class="summary-card small">
            <h4>Payments</h4>
            <p>₱{{ number_format($totalPayments ?? 0, 2) }}</p>
            <i class="fa-solid fa-wallet"></i>
        </div>
    </div>
</section>

<section class="filters">
    <button class="filter-btn active" data-status="all">All</button>
    <button class="filter-btn" data-status="pending">Pending</button>
    <button class="filter-btn" data-status="paid">Paid</button>
</section>

<section class="entries">
    @foreach ($clampings as $clamping)
        <div class="entry clickable-entry" data-status="{{ strtolower($clamping->status) }}" onclick="showClampingDetail({{ $clamping->id }})">
            <div class="entry-left">
                @if ($clamping->status == 'paid')
                    <i class="fa-solid fa-car"></i>
                @else
                    <i class="fa-solid fa-car-burst"></i>
                @endif

                <div class="entry-info">
                    <h4>{{ $clamping->ticket_no }}</h4>
                    <p>{{ \Carbon\Carbon::parse($clamping->created_at)->diffForHumans() }} • {{ $clamping->reason ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="entry-right">
                <p>₱{{ number_format($clamping->fine_amount, 2) }}</p>
                @if ($clamping->status == 'paid')
                    <small style="color:green;">Paid</small>
                @else
                    <small style="color:#888;">Unpaid</small>
                @endif
            </div>
        </div>
    @endforeach
</section>

<!-- Clamping Detail Modal -->
<div id="clampingDetailModal" class="clamping-detail-modal" onclick="closeModalOnBackdrop(event)">
    <div class="clamping-detail-content" onclick="event.stopPropagation()">
        <div class="clamping-detail-header">
            <h3>Clamping Details</h3>
            <button class="close-modal" onclick="closeClampingModal()">&times;</button>
        </div>
        <div id="clampingDetailBody">
            <!-- Details will be loaded here -->
        </div>
    </div>
</div>
@endsection

<style>
    .clickable-entry {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .clickable-entry:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        background: #f8f9ff;
    }

    .clickable-entry:active {
        transform: translateY(0);
    }

    .clamping-detail-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        overflow-y: auto;
        padding: 20px;
    }

    .clamping-detail-content {
        background: white;
        border-radius: 16px;
        padding: 25px;
        max-width: 500px;
        margin: 20px auto;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .clamping-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #eee;
    }

    .clamping-detail-header h3 {
        margin: 0;
        color: #333;
        font-size: 20px;
        font-weight: 600;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 28px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .close-modal:hover {
        color: #333;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #666;
        font-weight: 500;
        font-size: 14px;
    }

    .detail-value {
        color: #333;
        font-weight: 600;
        font-size: 14px;
        text-align: right;
        max-width: 60%;
        word-break: break-word;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-paid {
        background: #d4edda;
        color: #155724;
    }

    .status-approved {
        background: #cce5ff;
        color: #004085;
    }

    .status-accepted {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    @media (max-width: 768px) {
        .clamping-detail-content {
            margin: 10px;
            padding: 20px;
        }
    }
</style>

@push('scripts')
<script src="{{ asset('js/filter.js') }}"></script>
<script>
    const clampingData = @json($clampings);

    function showClampingDetail(clampingId) {
        const clamping = clampingData.find(c => c.id === clampingId);
        if (!clamping) return;

        const modal = document.getElementById('clampingDetailModal');
        const body = document.getElementById('clampingDetailBody');
        
        const statusClass = clamping.status ? `status-${clamping.status.toLowerCase()}` : '';
        const statusBadge = clamping.status 
            ? `<span class="status-badge ${statusClass}">${clamping.status.charAt(0).toUpperCase() + clamping.status.slice(1)}</span>`
            : 'N/A';

        const dateClamped = clamping.date_clamped 
            ? new Date(clamping.date_clamped).toLocaleString('en-PH', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit' 
            })
            : new Date(clamping.created_at).toLocaleString('en-PH', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit' 
            });

        body.innerHTML = `
            <div class="detail-row">
                <span class="detail-label">Ticket No:</span>
                <span class="detail-value">${clamping.ticket_no || 'N/A'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Plate No:</span>
                <span class="detail-value">${clamping.plate_no || 'N/A'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Vehicle Type:</span>
                <span class="detail-value">${clamping.vehicle_type || 'N/A'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Reason:</span>
                <span class="detail-value">${clamping.reason || 'N/A'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">${clamping.location || 'N/A'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Fine Amount:</span>
                <span class="detail-value">₱${parseFloat(clamping.fine_amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">${statusBadge}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date Clamped:</span>
                <span class="detail-value">${dateClamped}</span>
            </div>
            ${clamping.photo ? `
            <div class="detail-row">
                <span class="detail-label">Photo:</span>
                <span class="detail-value">
                    <img src="/storage/${clamping.photo}" alt="Clamping Photo" style="max-width: 100%; border-radius: 8px; margin-top: 8px;">
                </span>
            </div>
            ` : ''}
        `;

        modal.style.display = 'flex';
    }

    function closeClampingModal() {
        document.getElementById('clampingDetailModal').style.display = 'none';
    }

    function closeModalOnBackdrop(event) {
        if (event.target.id === 'clampingDetailModal') {
            closeClampingModal();
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeClampingModal();
        }
    });
</script>
@endpush
