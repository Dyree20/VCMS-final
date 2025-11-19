@extends('layouts.app')

@section('title', 'Enforcer GPS Tracking')

@section('content')
<div class="tracking-container">
    <!-- Header -->
    <div class="tracking-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class='bx bx-map-pin'></i>
                Enforcer GPS Tracking
            </h1>
            <p class="page-subtitle">Real-time location tracking of field enforcers</p>
        </div>
    </div>

    <!-- Enforcers List -->
    <div class="enforcers-grid">
        @forelse($enforcers as $location)
            <div class="enforcer-card">
                <div class="enforcer-header">
                    <div class="enforcer-avatar">
                        @if($location->user && $location->user->details && $location->user->details->photo)
                            <img src="{{ asset('storage/' . $location->user->details->photo) }}" alt="{{ $location->user->f_name }}">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar">
                        @endif
                    </div>
                    <div class="enforcer-info">
                        <h3 class="enforcer-name">{{ $location->user->f_name ?? 'Unknown' }} {{ $location->user->l_name ?? '' }}</h3>
                        <p class="enforcer-email">{{ $location->user->email ?? 'N/A' }}</p>
                    </div>
                    <div class="status-badge" data-status="{{ strtolower($location->status) }}">
                        <span class="status-dot"></span>
                        <span class="status-text">{{ ucfirst($location->status) }}</span>
                    </div>
                </div>

                <div class="enforcer-details">
                    <div class="detail-item">
                        <i class='bx bx-map-pin'></i>
                        <div>
                            <label>Current Location</label>
                            <p class="location-coords">{{ number_format($location->latitude, 6) }}, {{ number_format($location->longitude, 6) }}</p>
                            <p class="location-address">{{ $location->address ?? 'Address not available' }}</p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class='bx bx-target-lock'></i>
                        <div>
                            <label>Accuracy</label>
                            <p>{{ $location->accuracy_meters }} meters</p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class='bx bx-time'></i>
                        <div>
                            <label>Last Update</label>
                            <p>{{ $location->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>

                <div class="enforcer-actions">
                    <a href="{{ route('tracking.enforcer', $location->user_id) }}" class="btn-track">
                        <i class='bx bx-map'></i>
                        <span>View History</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class='bx bx-inbox'></i>
                <p>No enforcers with location data</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .tracking-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .tracking-header {
        padding: 28px 32px;
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: white;
    }

    .header-content h1 {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .header-content h1 i {
        font-size: 32px;
    }

    .page-subtitle {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
        font-weight: 500;
    }

    .enforcers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 24px;
        padding: 32px;
    }

    .enforcer-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .enforcer-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
        border-color: #2b58ff;
    }

    .enforcer-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
    }

    .enforcer-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .enforcer-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .enforcer-info {
        flex: 1;
        min-width: 0;
    }

    .enforcer-name {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #333;
        line-height: 1.2;
    }

    .enforcer-email {
        margin: 4px 0 0 0;
        font-size: 12px;
        color: #999;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .status-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .status-badge[data-status="online"] {
        background: #e8f5e9;
        color: #388e3c;
    }

    .status-badge[data-status="offline"] {
        background: #ffebee;
        color: #c62828;
    }

    .status-badge[data-status="on_break"] {
        background: #fff3e0;
        color: #f57c00;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-badge[data-status="online"] .status-dot {
        background: #388e3c;
        animation: pulse 2s infinite;
    }

    .status-badge[data-status="offline"] .status-dot {
        background: #c62828;
    }

    .status-badge[data-status="on_break"] .status-dot {
        background: #f57c00;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .enforcer-details {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .detail-item {
        display: flex;
        gap: 12px;
    }

    .detail-item i {
        font-size: 20px;
        color: #2b58ff;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .detail-item label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        color: #999;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .detail-item p {
        margin: 0;
        font-size: 14px;
        color: #333;
        line-height: 1.4;
    }

    .location-coords {
        font-family: monospace;
        font-size: 12px !important;
        color: #666 !important;
    }

    .location-address {
        font-size: 13px !important;
        color: #999 !important;
    }

    .enforcer-actions {
        padding: 16px 20px;
        background: #f8f9fa;
        border-top: 1px solid #e0e0e0;
        display: flex;
        gap: 12px;
    }

    .btn-track {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-track:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 88, 255, 0.3);
    }

    .empty-state {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 80px 32px;
        text-align: center;
        color: #999;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    @media (max-width: 1024px) {
        .tracking-header {
            padding: 20px 24px;
        }

        .header-content h1 {
            font-size: 22px;
        }

        .enforcers-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 24px;
        }
    }

    @media (max-width: 768px) {
        .tracking-header {
            padding: 16px 16px;
        }

        .header-content h1 {
            font-size: 20px;
            gap: 8px;
        }

        .header-content h1 i {
            font-size: 24px;
        }

        .page-subtitle {
            font-size: 13px;
        }

        .enforcers-grid {
            grid-template-columns: 1fr;
            gap: 16px;
            padding: 16px;
        }

        .enforcer-header {
            gap: 12px;
            padding: 16px;
        }

        .enforcer-avatar {
            width: 48px;
            height: 48px;
        }

        .enforcer-name {
            font-size: 15px;
        }

        .detail-item {
            gap: 10px;
        }

        .detail-item i {
            font-size: 18px;
        }
    }

    @media (max-width: 480px) {
        .tracking-header {
            padding: 12px 12px;
        }

        .header-content h1 {
            font-size: 18px;
        }

        .enforcers-grid {
            grid-template-columns: 1fr;
            gap: 12px;
            padding: 12px;
        }

        .enforcer-card {
            border-radius: 8px;
        }

        .enforcer-header {
            gap: 10px;
            padding: 12px;
        }

        .enforcer-avatar {
            width: 44px;
            height: 44px;
        }

        .enforcer-name {
            font-size: 14px;
        }

        .enforcer-email {
            font-size: 11px;
        }

        .status-badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        .enforcer-details {
            padding: 16px;
            gap: 12px;
        }

        .detail-item label {
            font-size: 10px;
        }

        .detail-item p {
            font-size: 13px;
        }

        .enforcer-actions {
            padding: 12px;
        }

        .btn-track {
            font-size: 13px;
            padding: 8px 12px;
        }
    }
</style>

<script>
    // Auto-refresh tracking page every 30 seconds
    setInterval(function() {
        fetch(window.location.href, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse the new HTML
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            
            // Replace only the enforcers grid
            const oldGrid = document.querySelector('.enforcers-grid');
            const newGrid = newDoc.querySelector('.enforcers-grid');
            if (oldGrid && newGrid) {
                oldGrid.innerHTML = newGrid.innerHTML;
            }
        })
        .catch(error => console.error('Error refreshing tracking:', error));
    }, 30000); // Refresh every 30 seconds
</script>
@endsection
