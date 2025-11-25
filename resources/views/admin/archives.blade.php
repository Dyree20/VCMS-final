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

    /* Enforcer Cards Grid */
    .enforcers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .enforcer-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        text-align: center;
    }

    .enforcer-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.12);
        border-color: #007bff;
    }

    .enforcer-card.active {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        border-color: #0056b3;
    }

    .enforcer-id {
        font-size: 24px;
        font-weight: 700;
        color: #007bff;
        margin-bottom: 8px;
        font-family: 'Courier New', monospace;
    }

    .enforcer-card.active .enforcer-id {
        color: white;
    }

    .enforcer-name {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .enforcer-card.active .enforcer-name {
        color: white;
    }

    .record-count {
        font-size: 12px;
        color: #999;
        margin-top: 8px;
    }

    .enforcer-card.active .record-count {
        color: rgba(255,255,255,0.8);
    }

    .record-count-badge {
        display: inline-block;
        background: #f0f0f0;
        padding: 2px 8px;
        border-radius: 12px;
        font-weight: 600;
        color: #007bff;
    }

    .enforcer-card.active .record-count-badge {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    /* Records Section */
    .records-section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .records-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f0f0f0;
    }

    .records-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: #333;
    }

    .clear-selection {
        background: #6c757d;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s;
    }

    .clear-selection:hover {
        background: #5a6268;
    }

    .records-table {
        width: 100%;
        border-collapse: collapse;
    }

    .records-table thead {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        color: white;
    }

    .records-table th {
        padding: 14px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .records-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }

    .records-table tbody tr:hover {
        background-color: #f9f9f9;
    }

    .records-table td {
        padding: 12px 14px;
        font-size: 14px;
        color: #555;
    }

    .ticket-info {
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

    .timestamp {
        color: #999;
        font-size: 13px;
    }

    .no-selection {
        text-align: center;
        padding: 80px 20px;
        color: #999;
    }

    .no-selection i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .no-selection p {
        margin: 0;
        font-size: 16px;
    }

    .no-records {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .no-records i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .no-records p {
        margin: 0;
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .enforcers-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
        }

        .enforcer-card {
            padding: 16px;
        }

        .enforcer-id {
            font-size: 18px;
        }

        .enforcer-name {
            font-size: 12px;
        }
    }
</style>

<div class="archives-container">
    <!-- Header -->
    <div class="archives-header">
        <h2><i class="fa-solid fa-box" style="margin-right: 8px;"></i>Archives</h2>
        <p style="color: #999; margin: 0;">Click an enforcer card to view their archive records</p>
    </div>

    <!-- Enforcers Grid -->
    <div class="enforcers-grid" id="enforcersGrid">
        @forelse($enforcers as $enforcer)
            <div class="enforcer-card" onclick="selectEnforcer({{ $enforcer['id'] }}, '{{ $enforcer['enforcer_id'] }}', '{{ $enforcer['name'] }}')">
                <div class="enforcer-id">{{ $enforcer['enforcer_id'] }}</div>
                <div class="enforcer-name">{{ $enforcer['name'] }}</div>
                <div class="record-count">
                    <span class="record-count-badge">{{ $enforcer['count'] }} records</span>
                </div>
            </div>
        @empty
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #999;">
                <i class='bx bx-inbox' style="font-size: 48px; opacity: 0.5;"></i>
                <p style="margin: 15px 0 0 0;">No archived records found</p>
            </div>
        @endforelse
    </div>

    <!-- Records Display Section -->
    <div class="records-section">
        <div id="noSelection" class="no-selection">
            <i class='bx bx-arrow-to-left'></i>
            <p>Select an enforcer to view their archive records</p>
        </div>

        <div id="recordsContent" style="display: none;">
            <div class="records-header">
                <h3><span id="selectedEnforcerDisplay"></span>'s Archive Records</h3>
                <button class="clear-selection" onclick="clearSelection()">← Back to Cards</button>
            </div>

            <div id="recordsTableWrapper" style="overflow-x: auto;">
                <table class="records-table">
                    <thead>
                        <tr>
                            <th>Ticket No</th>
                            <th>Plate No</th>
                            <th>Fine Amount</th>
                            <th>Status</th>
                            <th>Archived Date</th>
                        </tr>
                    </thead>
                    <tbody id="recordsTableBody">
                    </tbody>
                </table>
            </div>

            <div id="noRecords" class="no-records" style="display: none;">
                <i class='bx bx-inbox'></i>
                <p>No archive records for this enforcer</p>
            </div>
        </div>
    </div>
</div>

<script>
    const archivesData = @json($archivesByEnforcer);
    const selectedEnforcers = document.querySelectorAll('.enforcer-card');

    function selectEnforcer(userId, enforcerId, name) {
        // Remove active class from all cards
        selectedEnforcers.forEach(card => card.classList.remove('active'));

        // Add active class to clicked card
        event.target.closest('.enforcer-card').classList.add('active');

        // Get records for this enforcer
        const records = archivesData[userId] || [];

        // Show/hide appropriate sections
        document.getElementById('noSelection').style.display = 'none';
        document.getElementById('recordsContent').style.display = 'block';

        // Update header
        document.getElementById('selectedEnforcerDisplay').textContent = `${enforcerId} - ${name}`;

        if (records.length > 0) {
            // Populate table
            const tbody = document.getElementById('recordsTableBody');
            tbody.innerHTML = '';

            records.forEach(record => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><span class="ticket-info">${record.ticket_no}</span></td>
                    <td>${record.plate_no}</td>
                    <td>₱${parseFloat(record.fine_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td>
                        <span class="status-badge status-${record.archived_status.toLowerCase()}">
                            ${record.archived_status.charAt(0).toUpperCase() + record.archived_status.slice(1)}
                        </span>
                    </td>
                    <td><span class="timestamp">${new Date(record.archived_date).toLocaleDateString()} ${new Date(record.archived_date).toLocaleTimeString()}</span></td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('noRecords').style.display = 'none';
            document.getElementById('recordsTableWrapper').style.display = 'block';
        } else {
            document.getElementById('recordsTableWrapper').style.display = 'none';
            document.getElementById('noRecords').style.display = 'block';
        }
    }

    function clearSelection() {
        selectedEnforcers.forEach(card => card.classList.remove('active'));
        document.getElementById('noSelection').style.display = 'block';
        document.getElementById('recordsContent').style.display = 'none';
    }
</script>

@endsection
