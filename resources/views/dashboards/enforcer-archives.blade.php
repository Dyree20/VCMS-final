@extends('dashboards.enforcer')

@section('title', 'Archives')

@section('content')
<style>
    .archives-section {
        padding: 20px 15px;
    }

    .archives-section header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #2b58ff;
    }

    .archives-section h2 {
        margin: 0;
        color: #333;
        font-size: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .archives-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .archive-card {
        background: white;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .archive-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #2b58ff;
    }

    .archive-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .ticket-badge {
        background: linear-gradient(135deg, #2b58ff, #6280ff);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
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

    .archive-card-body {
        margin-bottom: 12px;
    }

    .archive-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .archive-info-label {
        color: #888;
        font-weight: 500;
    }

    .archive-info-value {
        color: #333;
        font-weight: 600;
        text-align: right;
    }

    .archive-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid #eee;
        font-size: 12px;
        color: #999;
    }

    .archive-detail-modal {
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

    .archive-detail-content {
        background: white;
        border-radius: 16px;
        padding: 25px;
        max-width: 500px;
        margin: 20px auto;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .archive-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #eee;
    }

    .archive-detail-header h3 {
        margin: 0;
        color: #333;
        font-size: 20px;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
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

    @media (max-width: 768px) {
        .archives-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $user = auth()->user();
    $user->load('details');
    $profilePhoto = $user->details && $user->details->photo 
        ? asset('storage/' . $user->details->photo) 
        : asset('images/default-avatar.png');
@endphp
<div class="archives-section">
    <header>
        <div style="display: flex; align-items: center; gap: 10px;">
            <h2>
                <i class='bx bx-archive'></i> My Archives
            </h2>
            <p style="margin: 0; color: #999; font-size: 14px;">Total: {{ $archives->total() }} violations</p>
        </div>
        <a href="{{ route('enforcer.profile') }}" class="profile-link" title="View Profile">
            <img src="{{ $profilePhoto }}" alt="Profile" class="profile-picture" id="headerProfilePic" style="width: 40px; height: 40px;">
        </a>
    </header>

    @if($archives->count() > 0)
        <div class="archives-grid">
            @foreach($archives as $archive)
                <div class="archive-card" onclick="showArchiveDetail({{ $archive->id }})">
                    <div class="archive-card-header">
                        <span class="ticket-badge">{{ $archive->ticket_no }}</span>
                        <span class="status-badge status-{{ strtolower($archive->archived_status) }}">
                            {{ ucfirst($archive->archived_status) }}
                        </span>
                    </div>
                    <div class="archive-card-body">
                        <div class="archive-info-row">
                            <span class="archive-info-label">Plate No:</span>
                            <span class="archive-info-value">{{ $archive->plate_no }}</span>
                        </div>
                        <div class="archive-info-row">
                            <span class="archive-info-label">Vehicle Type:</span>
                            <span class="archive-info-value">{{ $archive->vehicle_type ?? 'N/A' }}</span>
                        </div>
                        <div class="archive-info-row">
                            <span class="archive-info-label">Fine Amount:</span>
                            <span class="archive-info-value">₱{{ number_format($archive->fine_amount, 2) }}</span>
                        </div>
                    </div>
                    <div class="archive-card-footer">
                        <span>{{ $archive->archived_date->format('M d, Y') }}</span>
                        <span>{{ $archive->archived_date->format('H:i') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        @if($archives->hasPages())
            <div class="pagination">
                {{ $archives->links() }}
            </div>
        @endif
    @else
        <div class="no-archives">
            <i class='bx bx-inbox'></i>
            <p>No archived violations yet</p>
        </div>
    @endif
</div>

<!-- Archive Detail Modal -->
<div id="archiveDetailModal" class="archive-detail-modal" onclick="closeModalOnBackdrop(event)">
    <div class="archive-detail-content" onclick="event.stopPropagation()">
        <div class="archive-detail-header">
            <h3>Archive Details</h3>
            <button class="close-modal" onclick="closeArchiveModal()">&times;</button>
        </div>
        <div id="archiveDetailBody">
            <!-- Details will be loaded here -->
        </div>
    </div>
</div>

<script>
    const archiveData = @json($archives->items());

    function showArchiveDetail(archiveId) {
        const archive = archiveData.find(a => a.id === archiveId);
        if (!archive) return;

        const modal = document.getElementById('archiveDetailModal');
        const body = document.getElementById('archiveDetailBody');
        
        body.innerHTML = `
            <div class="detail-row">
                <span class="detail-label">Ticket No:</span>
                <span class="detail-value">${archive.ticket_no}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Plate No:</span>
                <span class="detail-value">${archive.plate_no}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Vehicle Type:</span>
                <span class="detail-value">${archive.vehicle_type || 'N/A'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Reason:</span>
                <span class="detail-value">${archive.reason || 'N/A'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Location:</span>
                <span class="detail-value">${archive.location || 'N/A'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Fine Amount:</span>
                <span class="detail-value">₱${parseFloat(archive.fine_amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="status-badge status-${archive.archived_status.toLowerCase()}">
                        ${archive.archived_status.charAt(0).toUpperCase() + archive.archived_status.slice(1)}
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Archived Date:</span>
                <span class="detail-value">${new Date(archive.archived_date).toLocaleString('en-PH', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'})}</span>
            </div>
        `;
        
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeArchiveModal() {
        const modal = document.getElementById('archiveDetailModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function closeModalOnBackdrop(event) {
        if (event.target.id === 'archiveDetailModal') {
            closeArchiveModal();
        }
    }

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeArchiveModal();
        }
    });
</script>

@endsection
