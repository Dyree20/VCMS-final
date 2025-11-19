@extends('layouts.app')

@section('title', $team->name . ' - Team Details')

@section('content')
<div class="team-show-container">
    <!-- Header Section -->
    <div class="team-show-header">
        <a href="{{ route('teams.index') }}" class="team-show-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="team-show-title-section">
            <h1 class="team-show-title">{{ $team->name }}</h1>
            <p class="team-show-subtitle">Team details and member management</p>
        </div>
        <div class="team-show-actions">
            <a href="{{ route('teams.edit', $team) }}" class="team-show-btn team-show-btn-edit">
                <i class="fa-solid fa-pen-to-square"></i>
                Edit
            </a>
            <form action="{{ route('teams.destroy', $team) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this team?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="team-show-btn team-show-btn-delete">
                    <i class="fa-solid fa-trash"></i>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Info & Stats Grid -->
    <div class="team-show-cards-grid">
        <!-- Team Info Card -->
        <div class="team-show-card">
            <h3 class="team-show-card-title">
                <i class="fa-solid fa-info-circle"></i>
                Team Information
            </h3>
            
            <div class="team-show-info-field">
                <label>Team Name</label>
                <p>{{ $team->name }}</p>
            </div>

            <div class="team-show-info-field">
                <label>Description</label>
                <p>{{ $team->description ?? '-' }}</p>
            </div>

            <div class="team-show-info-field">
                <label>Notes</label>
                <p>{{ $team->notes ?? '-' }}</p>
            </div>

            <div class="team-show-info-field">
                <label>Created By</label>
                <p>{{ $team->creator->f_name }} {{ $team->creator->l_name }}</p>
                <span class="team-show-date">{{ $team->created_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
        </div>

        <!-- Team Stats Card -->
        <div class="team-show-card">
            <h3 class="team-show-card-title">
                <i class="fa-solid fa-chart-pie"></i>
                Team Statistics
            </h3>
            
            <div class="team-show-stats-grid">
                <div class="team-show-stat-box">
                    <div class="team-show-stat-value" style="color: #2b58ff;">{{ $team->members->count() }}</div>
                    <div class="team-show-stat-label">Total Members</div>
                </div>
                <div class="team-show-stat-box">
                    @php
                        $enforcerCount = $team->members->filter(function($member) {
                            return $member->role_id == 2;
                        })->count();
                    @endphp
                    <div class="team-show-stat-value" style="color: #16a34a;">{{ $enforcerCount }}</div>
                    <div class="team-show-stat-label">Active Enforcers</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Section -->
    <div class="team-show-members-card">
        <div class="team-show-members-header">
            <h3 class="team-show-members-title">
                <i class="fa-solid fa-people-group"></i>
                Team Members
            </h3>
            <button type="button" onclick="openAddEnforcerModal()" class="team-show-btn-add-member">
                <i class="fa-solid fa-plus"></i>
                Add Member
            </button>
        </div>

        @if($team->members()->count() > 0)
            <div class="team-show-members-list">
                @foreach($team->members as $member)
                    <div class="team-show-member-row">
                        <div class="team-show-member-info">
                            <div class="team-show-member-avatar">
                                {{ substr($member->f_name, 0, 1) }}{{ substr($member->l_name, 0, 1) }}
                            </div>
                            <div class="team-show-member-details">
                                <div class="team-show-member-name">{{ $member->f_name }} {{ $member->l_name }}</div>
                                <div class="team-show-member-email">{{ $member->email }}</div>
                            </div>
                        </div>
                        <div class="team-show-member-meta">
                            <span class="team-show-member-role">{{ $member->role->name ?? 'Enforcer' }}</span>
                        </div>
                        <div class="team-show-member-date">
                            @php
                                $assignedAt = $member->pivot->assigned_at ?? null;
                            @endphp
                            {{ $assignedAt ? \Carbon\Carbon::parse($assignedAt)->format('M d, Y') : '-' }}
                        </div>
                        <form action="{{ route('teams.remove-enforcer', $team) }}" method="POST" style="display: inline;" onsubmit="return confirm('Remove this member from the team?');">
                            @csrf
                            <input type="hidden" name="enforcer_id" value="{{ $member->id }}">
                            <button type="submit" class="team-show-member-remove-btn" title="Remove">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="team-show-empty-state">
                <i class="fa-solid fa-people-group"></i>
                <p>No members assigned yet. Add enforcers to this team.</p>
            </div>
        @endif
    </div>
</div>

<!-- Add Enforcer Modal -->
<div id="addEnforcerModal" class="team-show-modal">
    <div class="team-show-modal-content">
        <div class="team-show-modal-header">
            <h2>Add Team Member</h2>
            <button type="button" onclick="closeAddEnforcerModal()" class="team-show-modal-close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('teams.add-enforcer', $team) }}" method="POST">
            @csrf
            
            <label class="team-show-modal-label">
                Select Enforcer <span style="color: #dc3545;">*</span>
            </label>
            <select name="enforcer_id" class="team-show-modal-select" required>
                <option value="">-- Select an enforcer --</option>
                @foreach($availableEnforcers as $enforcer)
                    <option value="{{ $enforcer->id }}">{{ $enforcer->f_name }} {{ $enforcer->l_name }} ({{ $enforcer->email }})</option>
                @endforeach
            </select>

            <div class="team-show-modal-actions">
                <button type="submit" class="team-show-modal-btn team-show-modal-btn-submit">
                    <i class="fa-solid fa-check"></i>
                    Add Member
                </button>
                <button type="button" onclick="closeAddEnforcerModal()" class="team-show-modal-btn team-show-modal-btn-cancel">
                    <i class="fa-solid fa-xmark"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddEnforcerModal() {
        document.getElementById('addEnforcerModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeAddEnforcerModal() {
        document.getElementById('addEnforcerModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    document.getElementById('addEnforcerModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddEnforcerModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddEnforcerModal();
        }
    });
</script>

@endsection
