@extends('layouts.app')

@section('title', 'Parking Zones Management')

@section('content')
<div class="zones-container">
    <!-- Header -->
    <div class="zones-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class='bx bx-map'></i>
                Parking Zones
            </h1>
            <p class="page-subtitle">Manage parking restriction zones</p>
        </div>
        <a href="{{ route('zones.create') }}" class="btn-primary">
            <i class='bx bx-plus'></i>
            <span>Add New Zone</span>
        </a>
    </div>

    <!-- Zones Table -->
    <div class="zones-table-container">
        <table class="zones-table">
            <thead>
                <tr>
                    <th>Zone Name</th>
                    <th>Location</th>
                    <th>Radius (m)</th>
                    <th>Fine Amount (₱)</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($zones as $zone)
                    <tr>
                        <td class="zone-name">{{ $zone->name }}</td>
                        <td class="zone-location"><i class='bx bx-map-pin'></i> {{ number_format($zone->latitude, 4) }}, {{ number_format($zone->longitude, 4) }}</td>
                        <td class="zone-radius">{{ $zone->radius_meters }}</td>
                        <td class="zone-fine">₱{{ number_format($zone->fine_amount, 2) }}</td>
                        <td class="zone-status"><span class="badge status-{{ strtolower($zone->status) }}">{{ ucfirst($zone->status) }}</span></td>
                        <td class="zone-creator">{{ $zone->createdBy->f_name ?? 'Admin' }}</td>
                        <td class="zone-actions">
                            <a href="{{ route('zones.show', $zone) }}" class="action-btn view-btn" title="View">👁️</a>
                            <a href="{{ route('zones.edit', $zone) }}" class="action-btn edit-btn" title="Edit">✏️</a>
                            <form action="{{ route('zones.destroy', $zone) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this zone?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" title="Delete">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class='bx bx-inbox'></i>
                            <p>No parking zones found. <a href="{{ route('zones.create') }}">Create one now</a></p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $zones->links() }}
    </div>
</div>

<style>
    .zones-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .zones-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 28px 32px;
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: white;
        gap: 20px;
        flex-wrap: wrap;
    }

    .header-content h1 {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .page-subtitle {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
        font-weight: 500;
    }

    .btn-primary {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: white;
        color: #2b58ff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .zones-table-container {
        overflow-x: auto;
    }

    .zones-table {
        width: 100%;
        border-collapse: collapse;
    }

    .zones-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #e0e0e0;
    }

    .zones-table th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #333;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .zones-table th:nth-child(1) { min-width: 180px; } /* Zone Name */
    .zones-table th:nth-child(2) { min-width: 200px; } /* Location */
    .zones-table th:nth-child(3) { min-width: 120px; } /* Radius */
    .zones-table th:nth-child(4) { min-width: 150px; } /* Fine Amount */
    .zones-table th:nth-child(5) { min-width: 100px; } /* Status */
    .zones-table th:nth-child(6) { min-width: 130px; } /* Created By */
    .zones-table th:nth-child(7) { min-width: 120px; } /* Actions */

    .zones-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .zones-table tbody tr:hover {
        background: #f8f9fa;
    }

    .zones-table td {
        padding: 16px;
        font-size: 14px;
        color: #666;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .zones-table td:nth-child(1) { min-width: 180px; } /* Zone Name */
    .zones-table td:nth-child(2) { min-width: 200px; } /* Location */
    .zones-table td:nth-child(3) { min-width: 120px; } /* Radius */
    .zones-table td:nth-child(4) { min-width: 150px; } /* Fine Amount */
    .zones-table td:nth-child(5) { min-width: 100px; } /* Status */
    .zones-table td:nth-child(6) { min-width: 130px; } /* Created By */
    .zones-table td:nth-child(7) { min-width: 120px; white-space: normal; } /* Actions */

    .zone-name {
        font-weight: 600;
        color: #333;
    }

    .zone-location {
        white-space: nowrap;
    }

    .zone-radius {
        font-weight: 600;
        background: #e8f0ff;
        color: #2b58ff;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
        white-space: nowrap;
    }

    .zone-fine {
        font-weight: 700;
        color: #388e3c;
        white-space: nowrap;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active {
        background: #e8f5e9;
        color: #388e3c;
    }

    .status-inactive {
        background: #ffebee;
        color: #c62828;
    }

    .status-maintenance {
        background: #fff3e0;
        color: #f57c00;
    }

    .zone-actions {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        text-decoration: none;
    }

    .view-btn {
        background: #e3f2fd;
        color: #1976d2;
    }

    .view-btn:hover {
        background: #1976d2;
        color: white;
    }

    .edit-btn {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .edit-btn:hover {
        background: #7b1fa2;
        color: white;
    }

    .delete-btn {
        background: #ffebee;
        color: #c62828;
        padding: 0;
    }

    .delete-btn:hover {
        background: #c62828;
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 32px !important;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.3;
        display: block;
    }

    .empty-state a {
        color: #2b58ff;
        text-decoration: none;
        font-weight: 600;
    }

    .empty-state a:hover {
        text-decoration: underline;
    }

    .pagination-wrapper {
        padding: 20px 32px;
        background: #f8f9fa;
        border-top: 1px solid #e0e0e0;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 1024px) {
        .zones-header {
            padding: 20px 24px;
            flex-direction: column;
            align-items: flex-start;
        }

        .header-content h1 {
            font-size: 22px;
        }

        .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .zones-table th,
        .zones-table td {
            padding: 12px;
            font-size: 13px;
        }

        .zone-location {
            font-size: 11px;
        }
    }

    @media (max-width: 768px) {
        .zones-table-container {
            overflow-x: auto;
        }

        .zones-table th,
        .zones-table td {
            padding: 10px;
            font-size: 12px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            font-size: 16px;
        }

        .zone-actions {
            gap: 4px;
        }

        .zone-location {
            white-space: nowrap;
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .zones-header {
            padding: 16px 12px;
        }

        .header-content h1 {
            font-size: 18px;
            gap: 8px;
        }

        .zones-table th:nth-child(n+3),
        .zones-table td:nth-child(n+3) {
            display: none;
        }

        .zones-table th:last-child,
        .zones-table td:last-child {
            display: table-cell;
        }

        .zones-table td {
            padding: 8px;
        }
    }
</style>
@endsection
