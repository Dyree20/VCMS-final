@extends('layouts.app')

@section('title', 'Users Dashboard')

@section('content')
<div>

    <!-- Reports Section -->
    <div class="user-reports">
        <div class="card">
            <i class="fa-solid fa-users"></i>
            <p>Total Users</p>
            <h2 id="totalUsers">{{ $totalUsers }}</h2>
        </div>
        <div class="card">
            <i class="fa-solid fa-user-check"></i>
            <p>Active Users</p>
            <h2 id="activeUsers">{{ $activeUsers }}</h2>
        </div>
        <div class="card">
            <i class="fa-solid fa-hourglass-half"></i>
            <p>Pending Users</p>
            <h2 id="pendingUsers">{{ $pendingUsers }}</h2>
        </div>
        <div class="card">
            <i class="fa-solid fa-user-slash"></i>
            <p>Inactive Users</p>
            <h2 id="inactiveUsers">{{ $inactiveUsers }}</h2>
        </div>
    </div>

    
    <div class="userTable-container">
        <h2 class="page-title">User Management</h2>
        <!-- Filters -->
        <div class="filters">
            <input type="text" id="searchInput" placeholder="Search users...">
            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="inactive">Inactive</option>
            </select>
            <select id="roleFilter">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="enforcer">Enforcer</option>
                <option value="front desk">Front Desk</option>
            </select>
        </div>

        <!-- User Table -->
        <table id="userTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Account Status</th>
                    <th>Actions</th>
                    <th>Assigned Area</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                @php
                    $status = strtolower($user->status->status ?? 'unknown');
                    $roleName = strtolower($user->role->name ?? '');
                    // Map status for filter: approved = active, suspended = inactive
                    $filterStatus = match($status) {
                        'approved' => 'active',
                        'suspended' => 'inactive',
                        'pending' => 'pending',
                        default => '',
                    };
                @endphp
                <tr data-user-id="{{ $user->id }}" 
                    data-name="{{ strtolower($user->f_name . ' ' . $user->l_name) }}"
                    data-username="{{ strtolower($user->username) }}"
                    data-email="{{ strtolower($user->email) }}"
                    data-user-id-code="{{ strtolower($user->enforcer_id ?? 'n/a') }}"
                    data-role="{{ $roleName }}"
                    data-status="{{ $filterStatus }}">
                    <td class="user-profile">
                        <img src="{{ $user->details && $user->details->photo ? asset('storage/' . $user->details->photo) : asset('images/default-avatar.png') }}" 
                            alt="{{ $user->f_name }} {{ $user->l_name }}">
                        <span>{{ $user->f_name }} {{ $user->l_name }}</span>
                    </td>

                    <td>{{ $user->username }}</td>
                    <td><code style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px; font-weight: 600; color: #007bff;">{{ $user->enforcer_id ?? 'N/A' }}</code></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td class="role-cell">
                        @if($user->role_id === null)
                            <button class="select-role-btn" data-user-id="{{ $user->id }}" title="Assign a role to this user">
                                <i class="fa-solid fa-user-tag"></i> Select Role
                            </button>
                        @else
                            <span class="role-badge">{{ $user->role->name ?? '—' }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                            // map database statuses to CSS classes
                            $statusClass = match($status) {
                                'approved' => 'active',
                                'pending' => 'probation',
                                'suspended' => 'inactive',
                                default => '',
                            };
                        @endphp
                        <span class="status {{ $statusClass }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>

                    <td class="account-status-cell">
                        @php
                            $isActive = strtolower($user->status->status ?? '') === 'approved';
                            $statusText = $isActive ? 'Active' : 'Inactive';
                            $statusClass = $isActive ? 'account-active' : 'account-inactive';
                        @endphp
                        <span class="account-status-badge {{ $statusClass }}" data-user-id="{{ $user->id }}">
                            {{ $statusText }}
                        </span>
                        <button class="toggle-account-btn {{ $isActive ? 'active' : '' }}" 
                                data-user-id="{{ $user->id }}" 
                                data-current-status="{{ strtolower($user->status->status ?? '') }}"
                                title="{{ $isActive ? 'Deactivate Account' : 'Activate Account' }}">
                            <i class="fa-solid {{ $isActive ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                        </button>
                    </td>

                    <td class="actions">
                        <button class="view-btn">View</button>
                        @if(strtolower($user->status->status ?? '') === 'pending')
                            <button class="approve-btn">Approve</button>
                            <button class="reject-btn">Reject</button>
                        @endif
                    </td>

                    <td class="assigned-area-cell">
                        @if(strtolower($user->role->name ?? '') === 'enforcer')
                            @php
                                $teamZones = $user->teams->flatMap(function($team) {
                                    return $team->parkingZones;
                                })->unique('id')->pluck('name')->filter();

                                $assignedZoneLabels = collect();
                                $assignmentSource = null;

                                if ($teamZones->isNotEmpty()) {
                                    $assignedZoneLabels = $teamZones;
                                    $assignmentSource = 'team';
                                } elseif ($user->parkingZone) {
                                    $assignedZoneLabels = collect([$user->parkingZone->name]);
                                    $assignmentSource = 'direct';
                                } elseif (!empty($user->assigned_area)) {
                                    $assignedZoneLabels = collect([$user->assigned_area]);
                                    $assignmentSource = 'manual';
                                }
                            @endphp

                            @if($assignedZoneLabels->isNotEmpty())
                                <div class="assigned-area-tags">
                                    @foreach($assignedZoneLabels as $label)
                                        <span class="assigned-area-badge">{{ $label }}</span>
                                    @endforeach
                                </div>
                                @if($assignmentSource === 'team')
                                    <small class="assigned-area-hint">Inherited from parking zone linked to team</small>
                                @elseif($assignmentSource === 'direct')
                                    <small class="assigned-area-hint">Assigned directly to this enforcer</small>
                                @endif
                            @else
                                <span class="assigned-area-display">Not Assigned</span>
                            @endif
                        @else
                            <span style="color: #999;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>

<!-- Role Selection Modal -->
<div id="roleSelectionModal" class="role-modal">
    <div class="role-modal-content">
        <div class="role-modal-header">
            <h3>Assign Role to User</h3>
            <button id="roleModalClose" class="role-modal-close">&times;</button>
        </div>
        <div class="role-modal-body">
            <p class="role-modal-subtitle">Select a role for this user:</p>
            <div class="role-options-container">
                @php
                    $roles = \App\Models\Role::all();
                    $roleDescriptions = [
                        'Admin' => 'Full system access and administrative privileges',
                        'Enforcer' => 'Patrol and enforcement operations',
                        'Front Desk' => 'Reception and customer service',
                    ];
                @endphp
                @foreach($roles as $role)
                    <button class="role-option" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}">
                        <div class="role-option-icon">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="role-option-content">
                            <h4>{{ $role->name }}</h4>
                            <p>{{ $roleDescriptions[$role->name] ?? 'Role' }}</p>
                        </div>
                        <div class="role-option-arrow">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

<script src="{{ asset('js/user-actions.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const toggleAccountBtns = document.querySelectorAll('.toggle-account-btn');
    
    toggleAccountBtns.forEach(btn => {
        btn.addEventListener('click', async function() {
            const userId = this.getAttribute('data-user-id');
            const currentStatus = this.getAttribute('data-current-status');
            
            // Determine new status
            const isCurrentlyActive = currentStatus === 'approved';
            const newStatus = isCurrentlyActive ? 'suspended' : 'approved';
            const action = isCurrentlyActive ? 'deactivate' : 'activate';
            
            if (!confirm(`Are you sure you want to ${action} this account?`)) {
                return;
            }

            try {
                const response = await fetch(`/users/${userId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                });

                // Check if response is ok
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: 'Server error occurred' }));
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Update the badge
                    const badgeElement = document.querySelector(`.account-status-badge[data-user-id="${userId}"]`);
                    const btnElement = document.querySelector(`.toggle-account-btn[data-user-id="${userId}"]`);
                    const iconElement = btnElement.querySelector('i');
                    
                    if (data.status === 'approved') {
                        badgeElement.textContent = 'Active';
                        badgeElement.className = 'account-status-badge account-active';
                        badgeElement.setAttribute('data-user-id', userId);
                        iconElement.className = 'fa-solid fa-toggle-on';
                        btnElement.classList.add('active');
                        btnElement.setAttribute('title', 'Deactivate Account');
                    } else {
                        badgeElement.textContent = 'Inactive';
                        badgeElement.className = 'account-status-badge account-inactive';
                        badgeElement.setAttribute('data-user-id', userId);
                        iconElement.className = 'fa-solid fa-toggle-off';
                        btnElement.classList.remove('active');
                        btnElement.setAttribute('title', 'Activate Account');
                    }
                    
                    btnElement.setAttribute('data-current-status', data.status.toLowerCase());
                    
                    // Update status column as well
                    const statusCell = btnElement.closest('tr').querySelector('td:nth-child(7)');
                    if (statusCell) {
                        const statusSpan = statusCell.querySelector('.status');
                        if (statusSpan) {
                            statusSpan.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                            statusSpan.className = `status ${data.status === 'approved' ? 'active' : (data.status === 'pending' ? 'probation' : 'inactive')}`;
                        }
                    }

                    alert(`Account ${action}d successfully!`);
                    
                    // Reload page to refresh all data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert(data.message || 'Failed to update account status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'An error occurred while updating account status');
            }
        });
    });

    // Search and Filter Functionality
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const roleFilter = document.getElementById('roleFilter');
    const tableBody = document.querySelector('#userTable tbody');
    const allRows = Array.from(tableBody.querySelectorAll('tr'));

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter.value.toLowerCase();
        const roleValue = roleFilter.value.toLowerCase();

        allRows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const username = row.getAttribute('data-username') || '';
            const email = row.getAttribute('data-email') || '';
            const userIdCode = row.getAttribute('data-user-id-code') || '';
            const role = row.getAttribute('data-role') || '';
            const status = row.getAttribute('data-status') || '';

            // Search filter - check if search term matches any field
            const matchesSearch = !searchTerm || 
                name.includes(searchTerm) ||
                username.includes(searchTerm) ||
                email.includes(searchTerm) ||
                userIdCode.includes(searchTerm);

            // Status filter
            const matchesStatus = !statusValue || status === statusValue;

            // Role filter
            const matchesRole = !roleValue || role === roleValue;

            // Show row if all filters match
            if (matchesSearch && matchesStatus && matchesRole) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update "No results" message if needed
        updateNoResultsMessage();
    }

    function updateNoResultsMessage() {
        const visibleRows = allRows.filter(row => {
            const display = row.style.display;
            return !display || display === '' || display === 'table-row';
        });
        let noResultsMsg = document.getElementById('noResultsMessage');
        
        if (visibleRows.length === 0 && allRows.length > 0) {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('tr');
                noResultsMsg.id = 'noResultsMessage';
                noResultsMsg.innerHTML = `
                    <td colspan="10" style="text-align: center; padding: 40px; color: #999; font-size: 16px;">
                        <i class="fa-solid fa-search" style="font-size: 48px; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                        No users found matching your search criteria.
                    </td>
                `;
                tableBody.appendChild(noResultsMsg);
            }
        } else {
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    }

    // Add event listeners
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    roleFilter.addEventListener('change', filterTable);

    // Role Selection Modal Functionality
    const roleModal = document.getElementById('roleSelectionModal');
    const roleModalClose = document.getElementById('roleModalClose');
    const selectRoleBtns = document.querySelectorAll('.select-role-btn');
    let currentUserId = null;

    // Open modal when Select Role button is clicked
    selectRoleBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentUserId = this.getAttribute('data-user-id');
            roleModal.style.display = 'flex';
        });
    });

    // Close modal
    if (roleModalClose) {
        roleModalClose.addEventListener('click', function() {
            roleModal.style.display = 'none';
            currentUserId = null;
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === roleModal) {
            roleModal.style.display = 'none';
            currentUserId = null;
        }
    });

    // Handle role selection
    const roleOptions = document.querySelectorAll('.role-option');
    roleOptions.forEach(option => {
        option.addEventListener('click', function() {
            const roleId = this.getAttribute('data-role-id');
            const roleName = this.getAttribute('data-role-name');
            
            if (!currentUserId || !roleId) return;

            // Send request to assign role
            fetch(`/users/${currentUserId}/assign-role`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    role_id: roleId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI - find the row and update the role cell
                    const userRow = document.querySelector(`tr[data-user-id="${currentUserId}"]`);
                    if (userRow) {
                        const roleCell = userRow.querySelector('.role-cell');
                        roleCell.innerHTML = `<span class="role-badge">${roleName}</span>`;
                        userRow.setAttribute('data-role', roleName.toLowerCase());
                        
                        // Update enforcer_id if it was generated
                        if (data.enforcer_id && data.enforcer_id !== null) {
                            const userIdCell = userRow.querySelector('td:nth-child(3)');
                            if (userIdCell) {
                                userIdCell.innerHTML = `<code style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px; font-weight: 600; color: #007bff;">${data.enforcer_id}</code>`;
                            }
                        }
                    }
                    
                    // Close modal
                    roleModal.style.display = 'none';
                    currentUserId = null;
                    
                    // Show success message
                    showNotification(`Role assigned to user successfully!`, 'success');
                } else {
                    showNotification(data.message || 'Failed to assign role', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error assigning role. Please try again.', 'error');
            });
        });
    });

    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            color: white;
            padding: 15px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>