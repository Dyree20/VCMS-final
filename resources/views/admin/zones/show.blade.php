@extends('layouts.app')

@section('title', $zone->name)

@section('content')
<div class="zone-detail-container">
    <a href="{{ route('zones.index') }}" class="back-btn">← Back to Zones</a>
    
    <div class="zone-detail-wrapper">
        @if(session('success') || session('error'))
            <div class="zone-alert {{ session('success') ? 'zone-alert-success' : 'zone-alert-error' }}">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        @php
            $teamEnforcers = $zone->teams->flatMap(function($team) {
                return $team->members;
            })->unique('id');

            $directEnforcers = $zone->assignedEnforcers ?? collect();
            $allEnforcers = $teamEnforcers->merge($directEnforcers)->unique('id');
        @endphp
        <div class="zone-header">
            <h2>{{ $zone->name }}</h2>
            <div class="zone-meta">
                <span class="status-badge {{ $zone->status }}">{{ ucfirst($zone->status) }}</span>
                <span class="fine-badge">₱{{ number_format($zone->fine_amount, 2) }} Fine</span>
            </div>
        </div>

        @if($zone->description)
        <div class="zone-section">
            <h3>Description</h3>
            <p>{{ $zone->description }}</p>
        </div>
        @endif

        <div class="zone-section">
            <h3>Location Details</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <label>Latitude</label>
                    <code>{{ $zone->latitude }}</code>
                </div>
                <div class="detail-item">
                    <label>Longitude</label>
                    <code>{{ $zone->longitude }}</code>
                </div>
                <div class="detail-item">
                    <label>Coverage Radius</label>
                    <strong>{{ number_format($zone->radius_meters) }} meters</strong>
                </div>
                <div class="detail-item">
                    <label>Fine Amount</label>
                    <strong class="fine-text">₱{{ number_format($zone->fine_amount, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="zone-section">
            <h3>Zone Management</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">{{ $zone->clampings()->count() }}</div>
                    <div class="stat-label">Total Clampings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $allEnforcers->count() }}</div>
                    <div class="stat-label">Assigned Enforcers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $zone->clampings()->where('status', 'paid')->count() }}</div>
                    <div class="stat-label">Paid Violations</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $zone->clampings()->where('status', '!=', 'paid')->count() }}</div>
                    <div class="stat-label">Pending Violations</div>
                </div>
            </div>
        </div>

        <div class="zone-section">
            <h3>Assigned Teams & Coverage</h3>
            @if($zone->teams->count() > 0)
                <div class="zone-teams-grid">
                    @foreach($zone->teams as $team)
                        <div class="zone-team-card">
                            <div class="zone-team-card-header">
                                <div>
                                    <h4>{{ $team->name }}</h4>
                                    <p>{{ $team->members->count() }} member{{ $team->members->count() === 1 ? '' : 's' }}</p>
                                </div>
                                <form action="{{ route('zones.remove-team', [$zone->id, $team->id]) }}" method="POST" onsubmit="return confirm('Remove this team from the zone?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="remove-team-btn" title="Remove team">
                                        <i class='bx bx-x'></i>
                                    </button>
                                </form>
                            </div>
                            @if($team->members->count() > 0)
                                <ul class="zone-team-members">
                                    @foreach($team->members as $member)
                                        <li>
                                            <div>
                                                <strong>{{ $member->f_name }} {{ $member->l_name }}</strong>
                                                <span>{{ $member->role->name ?? 'Enforcer' }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="zone-team-empty">No members assigned to this team yet.</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="zone-team-empty" style="margin-bottom: 15px;">No teams assigned to this zone yet. Assign one below.</div>
            @endif

            <div class="zone-assign-team-card">
                <h4>Assign a Team to this Zone</h4>
                <form action="{{ route('zones.assign-team', $zone->id) }}" method="POST" class="zone-assign-team-form">
                    @csrf
                    <label for="team_id">Select Team</label>
                    <select name="team_id" id="team_id" required {{ $availableTeams->isEmpty() ? 'disabled' : '' }}>
                        <option value="">{{ $availableTeams->isEmpty() ? 'All teams are already assigned' : '-- Choose a team --' }}</option>
                        @foreach($availableTeams as $availableTeam)
                            <option value="{{ $availableTeam->id }}">
                                {{ $availableTeam->name }} ({{ $availableTeam->members_count }} member{{ $availableTeam->members_count === 1 ? '' : 's' }})
                            </option>
                        @endforeach
                    </select>
                    @error('team_id')
                        <p class="zone-form-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="zone-assign-team-btn" {{ $availableTeams->isEmpty() ? 'disabled' : '' }}>
                        <i class='bx bx-link'></i> Assign Team
                    </button>
                </form>
                <p class="zone-assign-hint">
                    Teams share their assigned area with every enforcer in the team. Once a team is linked here, all of its active enforcers will see this zone on their dashboards and in user management.
                </p>
            </div>
        </div>

        @if($allEnforcers->count() > 0)
        <div class="zone-section">
            <h3>Assigned Enforcers</h3>
            <table class="enforcers-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Total Clampings</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allEnforcers as $enforcer)
                    <tr>
                        <td>{{ $enforcer->f_name }} {{ $enforcer->l_name }}</td>
                        <td>{{ $enforcer->email }}</td>
                        <td>{{ $enforcer->phone ?? '—' }}</td>
                        <td>
                            @php
                                $statusClass = match(strtolower($enforcer->status->status ?? 'pending')) {
                                    'approved' => 'active',
                                    'pending' => 'probation',
                                    'suspended' => 'inactive',
                                    default => 'inactive',
                                };
                            @endphp
                            <span class="status {{ $statusClass }}">{{ $enforcer->status->status ?? 'Pending' }}</span>
                        </td>
                        <td>{{ $enforcer->clampings()->count() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="zone-actions">
            <a href="{{ route('zones.edit', $zone->id) }}" class="btn btn-primary">
                <i class='bx bx-edit'></i> Edit Zone
            </a>
            <button onclick="deleteZone()" class="btn btn-danger">
                <i class='bx bx-trash'></i> Delete Zone
            </button>
            <form id="deleteForm" action="{{ route('zones.destroy', $zone->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<style>
    .zone-detail-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }

    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        color: #2b58ff;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }

    .back-btn:hover {
        color: #1e42cc;
    }

    .zone-detail-wrapper {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .zone-alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .zone-alert-success {
        background: #e8f5e9;
        color: #1b5e20;
        border: 1px solid #c8e6c9;
    }

    .zone-alert-error {
        background: #ffebee;
        color: #b71c1c;
        border: 1px solid #ffcdd2;
    }

    .zone-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 32px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .zone-header h2 {
        color: #333;
        font-size: 28px;
        margin: 0;
    }

    .zone-meta {
        display: flex;
        gap: 12px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .status-badge.maintenance {
        background: #fff3cd;
        color: #856404;
    }

    .fine-badge {
        background: #e8f4f8;
        color: #0c5577;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
    }

    .zone-section {
        margin-bottom: 32px;
    }

    .zone-section h3 {
        color: #333;
        font-size: 16px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f0f0;
    }

    .zone-section p {
        color: #666;
        line-height: 1.6;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .detail-item {
        background: #f9f9f9;
        padding: 16px;
        border-radius: 8px;
        border-left: 3px solid #2b58ff;
    }

    .detail-item label {
        display: block;
        font-weight: 600;
        color: #666;
        font-size: 12px;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-item code {
        display: block;
        background: #fff;
        padding: 8px;
        border-radius: 4px;
        color: #2b58ff;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        font-size: 13px;
        word-break: break-all;
    }

    .detail-item strong {
        display: block;
        color: #333;
        font-size: 16px;
        margin-top: 4px;
    }

    .fine-text {
        color: #28a745;
        font-size: 18px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
    }

    .stat-card {
        background: linear-gradient(135deg, #f9f9f9 0%, #f0f0f0 100%);
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #e0e0e0;
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: #2b58ff;
    }

    .stat-label {
        font-size: 12px;
        color: #666;
        margin-top: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .enforcers-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .enforcers-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #e0e0e0;
    }

    .enforcers-table th {
        padding: 12px;
        text-align: left;
        font-weight: 700;
        color: #333;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .enforcers-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .enforcers-table tbody tr:hover {
        background: #f8f9fa;
    }

    .enforcers-table td {
        padding: 12px;
        font-size: 14px;
        color: #666;
    }

    .status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 12px;
    }

    .status.active {
        background: #d4edda;
        color: #155724;
    }

    .status.probation {
        background: #fff3cd;
        color: #856404;
    }

    .status.inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .zone-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #f0f0f0;
    }

    .zone-teams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .zone-team-card {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 16px;
        background: #fafbff;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .zone-team-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .zone-team-card-header h4 {
        margin: 0;
        font-size: 16px;
        color: #1f2937;
    }

    .zone-team-card-header p {
        margin: 2px 0 0 0;
        color: #6b7280;
        font-size: 13px;
    }

    .remove-team-btn {
        background: #ffebee;
        border: none;
        color: #c62828;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, color 0.2s;
    }

    .remove-team-btn:hover {
        background: #c62828;
        color: #fff;
    }

    .zone-team-members {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .zone-team-members li {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .zone-team-members strong {
        display: block;
        color: #111827;
    }

    .zone-team-members span {
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .zone-team-empty {
        padding: 12px;
        border-radius: 8px;
        background: #f3f4f6;
        color: #6b7280;
        text-align: center;
        font-size: 13px;
    }

    .zone-assign-team-card {
        border: 1px dashed #cdd5ff;
        border-radius: 12px;
        padding: 20px;
        background: #f8faff;
    }

    .zone-assign-team-card h4 {
        margin: 0 0 12px 0;
        color: #1f2a4a;
    }

    .zone-assign-team-form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .zone-assign-team-form label {
        font-weight: 600;
        color: #374151;
        font-size: 13px;
    }

    .zone-assign-team-form select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 10px 12px;
        font-size: 14px;
    }

    .zone-assign-team-btn {
        border: none;
        background: linear-gradient(135deg, #4f46e5, #2b58ff);
        color: #fff;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: opacity 0.2s;
        width: fit-content;
    }

    .zone-assign-team-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .zone-assign-hint {
        font-size: 12px;
        color: #6b7280;
        margin-top: 10px;
        line-height: 1.5;
    }

    .zone-form-error {
        color: #b91c1c;
        font-size: 12px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: #fff;
        flex: 1;
        justify-content: center;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1e42cc 0%, #152d99 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 88, 255, 0.3);
    }

    .btn-danger {
        background: #dc3545;
        color: #fff;
        flex: 1;
        justify-content: center;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    @media (max-width: 768px) {
        .zone-detail-container {
            padding: 10px;
        }

        .zone-detail-wrapper {
            padding: 20px;
        }

        .zone-header {
            flex-direction: column;
            gap: 12px;
        }

        .zone-header h2 {
            font-size: 22px;
        }

        .zone-meta {
            flex-wrap: wrap;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .zone-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<script>
    function deleteZone() {
        if (confirm('Are you sure you want to delete this parking zone? This action cannot be undone.')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endsection
