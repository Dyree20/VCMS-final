@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')

<div class="profile-container">
    <!-- Left Sidebar Navigation -->
    <aside class="profile-sidebar">
        <div class="profile-header">
            <div class="profile-avatar-wrapper">
                <img src="{{ $user->details && $user->details->photo ? asset('storage/' . $user->details->photo) : asset('images/default-avatar.png') }}" 
                     alt="{{ $user->f_name }}" class="profile-avatar">
            </div>
            <div class="profile-header-info">
                <h2>{{ $user->f_name }} {{ $user->l_name }}</h2>
                <p>{{ $user->role->name ?? 'User' }}</p>
            </div>
        </div>

        <ul class="profile-nav-menu">
            <li class="profile-nav-item">
                <a href="#personal-info" class="profile-nav-link active" data-section="personal">
                    <i class='fa-solid fa-user'></i>
                    <span>Personal Information</span>
                </a>
            </li>
            <li class="profile-nav-item">
                <a href="#security" class="profile-nav-link" data-section="security">
                    <i class='fa-solid fa-shield'></i>
                    <span>Login & Password</span>
                </a>
            </li>
            <li class="profile-nav-item">
                <a href="#notifications" class="profile-nav-link" data-section="notifications">
                    <i class='fa-solid fa-bell'></i>
                    <span>Notifications</span>
                </a>
            </li>
            @if($user->role->name === 'Admin')
                <li class="profile-nav-item">
                    <a href="#teams" class="profile-nav-link" data-section="teams">
                        <i class='fa-solid fa-people-group'></i>
                        <span>Teams</span>
                    </a>
                </li>
            @elseif($user->role->name === 'Enforcer')
                <li class="profile-nav-item">
                    <a href="#location" class="profile-nav-link" data-section="location">
                        <i class='fa-solid fa-location-dot'></i>
                        <span>GPS Location</span>
                    </a>
                </li>
            @endif
        </ul>
    </aside>

    <!-- Right Content Area -->
    <main class="profile-content-area">
        <!-- Personal Information Section -->
        <section class="profile-card" id="personal-info">
            <h3 class="profile-card-title">
                <i class='fa-solid fa-user'></i>
                Personal Information
            </h3>

            <div class="profile-form-grid">
                <!-- First Name & Last Name -->
                <div class="profile-form-group">
                    <label class="profile-form-label">First Name</label>
                    <span class="profile-form-value">{{ $user->f_name }}</span>
                </div>

                <div class="profile-form-group">
                    <label class="profile-form-label">Last Name</label>
                    <span class="profile-form-value">{{ $user->l_name }}</span>
                </div>

                <!-- Email -->
                <div class="profile-form-group full">
                    <label class="profile-form-label">Email</label>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span class="profile-form-value">{{ $user->email }}</span>
                        <div class="verified-badge">
                            <i class='fa-solid fa-check-circle'></i>
                            Verified
                        </div>
                    </div>
                </div>

                <!-- Phone & Gender -->
                <div class="profile-form-group">
                    <label class="profile-form-label">Phone Number</label>
                    <span class="profile-form-value">{{ $user->phone ?? '—' }}</span>
                </div>

                <div class="profile-form-group">
                    <label class="profile-form-label">Gender</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->gender ? ucfirst($user->details->gender) : '—' }}
                    </span>
                </div>

                <!-- Address -->
                <div class="profile-form-group full">
                    <label class="profile-form-label">Address</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->address ? $user->details->address : '—' }}
                    </span>
                </div>

                <!-- Date of Birth & Username -->
                <div class="profile-form-group">
                    <label class="profile-form-label">Date of Birth</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->birthdate ? $user->details->birthdate->format('M d, Y') : '—' }}
                    </span>
                </div>

                <div class="profile-form-group">
                    <label class="profile-form-label">Username</label>
                    <span class="profile-form-value">{{ $user->username }}</span>
                </div>

                <!-- Status & Role -->
                <div class="profile-form-group">
                    <label class="profile-form-label">Status</label>
                    <span class="profile-form-value" style="text-transform: capitalize;">
                        {{ $user->status->status ?? '—' }}
                    </span>
                </div>

                <div class="profile-form-group">
                    <label class="profile-form-label">Role</label>
                    <span class="profile-form-value">{{ $user->role->name ?? '—' }}</span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="{{ route('admin.profile.edit') }}" class="profile-btn profile-btn-primary">
                    <i class='fa-solid fa-edit'></i> Edit Profile
                </a>
                <button class="profile-btn profile-btn-secondary" onclick="window.history.back()">
                    <i class='fa-solid fa-arrow-left'></i> Back
                </button>
            </div>
        </section>

        <!-- Login & Password Section -->
        <section class="profile-card" id="security" style="display: none;">
            <h3 class="profile-card-title">
                <i class='fa-solid fa-shield'></i>
                Login & Password
            </h3>

            <div class="profile-form-grid">
                <div class="profile-form-group full">
                    <label class="profile-form-label">Username</label>
                    <span class="profile-form-value">{{ $user->username }}</span>
                </div>

                <div class="profile-form-group full">
                    <label class="profile-form-label">Current Password</label>
                    <input type="password" class="profile-form-input" placeholder="••••••••" disabled>
                </div>

                <div class="profile-form-group full">
                    <label class="profile-form-label">New Password</label>
                    <input type="password" class="profile-form-input" placeholder="Enter new password">
                </div>

                <div class="profile-form-group full">
                    <label class="profile-form-label">Confirm Password</label>
                    <input type="password" class="profile-form-input" placeholder="Confirm new password">
                </div>
            </div>

            <div class="profile-actions">
                <button class="profile-btn profile-btn-primary" onclick="alert('Change password feature coming soon')">
                    Update Password
                </button>
                <button class="profile-btn profile-btn-secondary" onclick="document.getElementById('personal-info').scrollIntoView(); selectSection('personal')">
                    Cancel
                </button>
            </div>
        </section>

        <!-- Notifications Section -->
        <section class="profile-card" id="notifications" style="display: none;">
            <h3 class="profile-card-title">
                <i class='fa-solid fa-bell'></i>
                Notification Preferences
            </h3>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e8eaed;">
                    <div>
                        <span class="profile-form-label" style="margin-bottom: 4px; display: block;">Email Notifications</span>
                        <p style="font-size: 12px; color: #999; margin: 0;">Receive updates via email</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e8eaed;">
                    <div>
                        <span class="profile-form-label" style="margin-bottom: 4px; display: block;">SMS Alerts</span>
                        <p style="font-size: 12px; color: #999; margin: 0;">Receive SMS alerts for urgent items</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e8eaed;">
                    <div>
                        <span class="profile-form-label" style="margin-bottom: 4px; display: block;">Push Notifications</span>
                        <p style="font-size: 12px; color: #999; margin: 0;">Get browser notifications</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                    <div>
                        <span class="profile-form-label" style="margin-bottom: 4px; display: block;">Marketing Emails</span>
                        <p style="font-size: 12px; color: #999; margin: 0;">Promotional messages and updates</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div class="profile-actions">
                <button class="profile-btn profile-btn-primary" onclick="alert('Preferences saved!')">
                    Save Preferences
                </button>
                <button class="profile-btn profile-btn-secondary" onclick="selectSection('personal')">
                    Cancel
                </button>
            </div>
        </section>

        @if($user->role->name === 'Enforcer')
        <!-- GPS Location Tracking Section -->
        <section class="profile-card" id="location" style="display: none;">
            <h3 class="profile-card-title">
                <i class='fa-solid fa-location-dot'></i>
                GPS Location Tracking
            </h3>

            <div class="location-section">
                <div class="location-item">
                    <div>
                        <span class="location-label">Location Tracking Status</span>
                        <p style="font-size: 12px; color: #666; margin: 4px 0 0;">Enable GPS tracking for real-time location monitoring</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" {{ auth()->user()->location_tracking_enabled ? 'checked' : '' }} onchange="toggleLocationTracking()">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div style="background: #fff3e0; border-left: 4px solid #ff9500; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0; font-size: 13px; color: #e65100;">
                    <i class='fa-solid fa-info-circle'></i>
                    When enabled, admins can see your real-time location while you're on duty
                </p>
            </div>

            <div class="profile-actions">
                <button class="profile-btn profile-btn-primary" onclick="alert('Location tracking updated!')">
                    Save Settings
                </button>
                <button class="profile-btn profile-btn-secondary" onclick="selectSection('personal')">
                    Cancel
                </button>
            </div>
        </section>
        @endif
    </main>
</div>

<script>
    // Section Navigation
    function selectSection(sectionName) {
        // Hide all sections
        document.querySelectorAll('.profile-card').forEach(card => {
            card.style.display = 'none';
        });

        // Remove active class from all nav links
        document.querySelectorAll('.profile-nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // Show selected section
        if (sectionName === 'personal') {
            document.getElementById('personal-info').style.display = 'block';
        } else if (sectionName === 'security') {
            document.getElementById('security').style.display = 'block';
        } else if (sectionName === 'notifications') {
            document.getElementById('notifications').style.display = 'block';
        } else if (sectionName === 'location') {
            document.getElementById('location').style.display = 'block';
        }

        // Set active nav link
        document.querySelector(`[data-section="${sectionName}"]`).classList.add('active');
    }

    // Sidebar Navigation Event Listeners
    document.querySelectorAll('.profile-nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            selectSection(section);
        });
    });

    // Location Tracking Toggle
    function toggleLocationTracking() {
        // This would make an AJAX call to update the database
        console.log('Location tracking toggled');
    }

    // Mobile: Hide personal info and show first section on load
    if (window.innerWidth <= 768) {
        selectSection('personal');
    }
</script>

<style>
    @media (max-width: 768px) {
        .profile-card {
            border-radius: 12px;
        }
    }
</style>
@endsection
