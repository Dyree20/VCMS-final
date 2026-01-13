@extends('layouts.app')

@section('title', 'User Details')

<style>
/* Role Change Modal Styles */
#roleChangeModal {
  display: none !important;
  position: fixed !important;
  z-index: 1000 !important;
  left: 0 !important;
  top: 0 !important;
  width: 100% !important;
  height: 100% !important;
  background-color: rgba(0, 0, 0, 0.5) !important;
}

#roleChangeModal.active {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  animation: fadeIn 0.3s ease !important;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    transform: translateY(50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.role-modal-content {
  background: white !important;
  padding: 0 !important;
  border-radius: 12px !important;
  width: 90% !important;
  max-width: 500px !important;
  max-height: 80vh !important;
  overflow-y: auto !important;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
  animation: slideUp 0.3s ease !important;
  display: flex !important;
  flex-direction: column !important;
}

.role-modal-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  padding: 20px !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  border-radius: 12px 12px 0 0 !important;
  flex-shrink: 0 !important;
}

.role-modal-header h3 {
  margin: 0 !important;
  font-size: 18px !important;
  font-weight: 600 !important;
  color: white !important;
}

.role-modal-close {
  background: none !important;
  border: none !important;
  color: white !important;
  font-size: 28px !important;
  cursor: pointer !important;
  padding: 0 !important;
  width: 30px !important;
  height: 30px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: transform 0.2s ease !important;
  flex-shrink: 0 !important;
}

.role-modal-close:hover {
  transform: scale(1.2) !important;
}

.role-modal-body {
  padding: 25px !important;
  overflow-y: auto !important;
  flex-grow: 1 !important;
}

.role-modal-subtitle {
  margin: 0 0 20px 0 !important;
  color: #666 !important;
  font-size: 14px !important;
  font-weight: 500 !important;
}

.role-options-container {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}

.role-option-change {
  background: #f8f9fa !important;
  border: 2px solid #e9ecef !important;
  border-radius: 8px !important;
  padding: 15px !important;
  cursor: pointer !important;
  display: flex !important;
  align-items: center !important;
  gap: 15px !important;
  transition: all 0.3s ease !important;
  text-align: left !important;
  width: 100% !important;
}

.role-option-change:hover {
  background: #f0f0ff !important;
  border-color: #667eea !important;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15) !important;
  transform: translateX(4px) !important;
}

.role-option-change:active {
  background: #e8e8ff !important;
  border-color: #764ba2 !important;
  transform: translateX(2px) !important;
}

.role-option-icon {
  width: 45px !important;
  height: 45px !important;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  border-radius: 8px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: white !important;
  font-size: 22px !important;
  flex-shrink: 0 !important;
}

.role-option-content {
  flex: 1 !important;
  min-width: 0 !important;
}

.role-option-content h4 {
  margin: 0 0 4px 0 !important;
  font-size: 15px !important;
  font-weight: 600 !important;
  color: #333 !important;
}

.role-option-content p {
  margin: 0 !important;
  font-size: 12px !important;
  color: #999 !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

.role-option-arrow {
  color: #ccc !important;
  font-size: 16px !important;
  flex-shrink: 0 !important;
  transition: color 0.3s ease, transform 0.3s ease !important;
}

.role-option-change:hover .role-option-arrow {
  color: #667eea !important;
  transform: translateX(4px) !important;
}

@media (max-width: 600px) {
  .role-modal-content {
    width: 95% !important;
    max-height: 90vh !important;
  }

  .role-modal-header {
    padding: 15px !important;
  }

  .role-modal-header h3 {
    font-size: 16px !important;
  }

  .role-modal-body {
    padding: 15px !important;
  }

  .role-option-change {
    padding: 12px !important;
    gap: 12px !important;
  }

  .role-option-icon {
    width: 40px !important;
    height: 40px !important;
    font-size: 18px !important;
  }

  .role-option-content h4 {
    font-size: 14px !important;
  }

  .role-option-content p {
    font-size: 11px !important;
  }
}
</style>

@section('content')
<div class="user-detail-page">
    <a href="{{ route('users') }}" class="btn">← Back to Users</a>

    <div class="user-card">
        <div class="user-header">
            <img src="{{ $user->details && $user->details->photo ? asset('storage/' . $user->details->photo) : asset('images/default-avatar.png') }}" alt="{{ $user->f_name }}">
            <h2>{{ $user->f_name }} {{ $user->l_name }}</h2>
            <p><strong>Username:</strong> {{ $user->username }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone ?? '—' }}</p>
            <p><strong>User ID:</strong> <code style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px; font-weight: 600; color: #007bff;">{{ $user->enforcer_id ?? 'Not Assigned' }}</code></p>
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #eee;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <p style="margin: 0;"><strong>Role:</strong> 
                            @if($user->role_id === null)
                                <span style="color: #999;">Not Assigned</span>
                            @else
                                <span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; border-left: 3px solid #667eea;">{{ $user->role->name ?? '—' }}</span>
                            @endif
                        </p>
                    </div>
                    <button id="changeRoleBtn" class="btn-small" style="padding: 6px 12px; font-size: 13px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; transition: all 0.2s;" title="Change user role">Change Role</button>
                </div>
            </div>
            <p style="margin-top: 12px;"><strong>Status:</strong> {{ $user->status->status ?? '—' }}</p>
        </div>

        <div class="user-meta">
            <h3>Details</h3>
            @if($user->details)
                <p><strong>Address:</strong> {{ $user->details->address ?? '—' }}</p>
                <p><strong>Birthdate:</strong> {{ optional($user->details->birthdate)->format('Y-m-d') ?? '—' }}</p>
                <p><strong>Gender:</strong> {{ $user->details->gender ?? '—' }}</p>
            @else
                <p>No additional details.</p>
            @endif
        </div>

        <div class="user-actions" style="margin-top:12px;">
            @if(strtolower($user->status->status ?? '') === 'pending')
                <form id="approveForm" action="{{ route('users.approve', $user->id) }}" method="POST" style="display:inline">
                    @csrf
                    <button class="auth-button">Approve</button>
                </form>

                <form id="rejectForm" action="{{ route('users.reject', $user->id) }}" method="POST" style="display:inline">
                    @csrf
                    <button class="auth-button" style="background:#c0392b">Reject</button>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Role Change Modal -->
<div id="roleChangeModal">
    <div class="role-modal-content">
        <div class="role-modal-header">
            <h3>Change User Role</h3>
            <button id="roleChangeModalClose" class="role-modal-close">&times;</button>
        </div>
        <div class="role-modal-body">
            <p class="role-modal-subtitle">Select a new role for {{ $user->f_name }} {{ $user->l_name }}:</p>
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
                    <button class="role-option-change" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}" style="{{ $user->role_id == $role->id ? 'border: 2px solid #667eea; background: #f0f0ff;' : '' }}">
                        <div class="role-option-icon">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="role-option-content">
                            <h4>{{ $role->name }}</h4>
                            <p>{{ $roleDescriptions[$role->name] ?? 'Role' }}</p>
                        </div>
                        <div class="role-option-arrow">
                            @if($user->role_id == $role->id)
                                <i class="fa-solid fa-check" style="color: #667eea;"></i>
                            @else
                                <i class="fa-solid fa-chevron-right"></i>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const changeRoleBtn = document.getElementById('changeRoleBtn');
    const roleChangeModal = document.getElementById('roleChangeModal');
    const roleChangeModalClose = document.getElementById('roleChangeModalClose');
    const roleOptions = document.querySelectorAll('.role-option-change');
    const userId = '{{ $user->id }}';

    // Check if elements exist before adding listeners
    if (!changeRoleBtn || !roleChangeModal || !roleChangeModalClose) {
        console.error('Required modal elements not found');
        return;
    }

    // Open modal
    changeRoleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        roleChangeModal.classList.add('active');
    });

    // Close modal
    roleChangeModalClose.addEventListener('click', function(e) {
        e.preventDefault();
        roleChangeModal.classList.remove('active');
    });

    // Close on outside click
    window.addEventListener('click', function(event) {
        if (event.target === roleChangeModal) {
            roleChangeModal.classList.remove('active');
        }
    });

    // Handle role selection
    if (roleOptions.length > 0) {
        roleOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const roleId = this.getAttribute('data-role-id');
                const roleName = this.getAttribute('data-role-name');
                
                fetch(`/users/${userId}/assign-role`, {
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
                        // Update page content
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to change role'));
                        roleChangeModal.classList.remove('active');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error changing role. Please try again.');
                    roleChangeModal.classList.remove('active');
                });
            });
        });
    }
});
</script>
