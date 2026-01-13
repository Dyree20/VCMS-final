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
            <div class="profile-card-header">
                <h3 class="profile-card-title">
                    <i class='fa-solid fa-user'></i>
                    Personal Information
                </h3>
                <a href="{{ route('admin.profile.edit') }}" class="profile-edit-btn">
                    <i class='fa-solid fa-edit'></i> Edit
                </a>
            </div>

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
                    <div class="profile-form-row">
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

                <!-- Date of Birth -->
                <div class="profile-form-group">
                    <label class="profile-form-label">Date of Birth</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->birth_date ? $user->details->birth_date->format('M d, Y') : '—' }}
                    </span>
                </div>

                <!-- Address -->
                <div class="profile-form-group full">
                    <label class="profile-form-label">Address</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->address ? $user->details->address : '—' }}
                    </span>
                </div>

                <!-- City, Country & Postal Code -->
                <div class="profile-form-group">
                    <label class="profile-form-label">City</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->city ? $user->details->city : '—' }}
                    </span>
                </div>

                <div class="profile-form-group">
                    <label class="profile-form-label">Country</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->country ? $user->details->country : '—' }}
                    </span>
                </div>

                <div class="profile-form-group">
                    <label class="profile-form-label">Postal Code</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->postal_code ? $user->details->postal_code : '—' }}
                    </span>
                </div>

                <!-- ID Type & ID Number -->
                <div class="profile-form-group">
                    <label class="profile-form-label">ID Type</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->id_type ? $user->details->id_type : '—' }}
                    </span>
                </div>

                <div class="profile-form-group">
                    <label class="profile-form-label">ID Number</label>
                    <span class="profile-form-value">
                        {{ $user->details && $user->details->id_number ? $user->details->id_number : '—' }}
                    </span>
                </div>

                <!-- Username, Status & Role -->
                <div class="profile-form-group">
                    <label class="profile-form-label">Username</label>
                    <span class="profile-form-value">{{ $user->username }}</span>
                </div>

                <div class="profile-form-group">
                    <label class="profile-form-label">Status</label>
                    <span class="profile-form-value status-badge status-{{ strtolower($user->status->status ?? 'active') }}">
                        {{ $user->status->status ?? '—' }}
                    </span>
                </div>

                <div class="profile-form-group">
                    <label class="profile-form-label">Role</label>
                    <span class="profile-form-value role-badge">{{ $user->role->name ?? '—' }}</span>
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
    :root {
        --primary-color: #2b58ff;
        --primary-dark: #1e40af;
        --primary-light: #e3f2fd;
        --secondary-color: #f0f2f5;
        --text-primary: #1f2937;
        --text-secondary: #666;
        --border-color: #e8eaed;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .profile-container {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 24px;
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
        background: #f9fafb;
        min-height: 100vh;
    }

    /* ===== SIDEBAR ===== */
    .profile-sidebar {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        height: fit-content;
        position: sticky;
        top: 20px;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border-color);
    }

    .profile-avatar-wrapper {
        margin-bottom: 16px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-light);
        display: block;
        margin: 0 auto;
    }

    .profile-header-info h2 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 12px 0 4px;
    }

    .profile-header-info p {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .profile-nav-menu {
        list-style: none;
    }

    .profile-nav-item {
        margin-bottom: 8px;
    }

    .profile-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 8px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .profile-nav-link:hover {
        background: var(--secondary-color);
        color: var(--primary-color);
    }

    .profile-nav-link.active {
        background: var(--primary-light);
        color: var(--primary-color);
        font-weight: 600;
    }

    .profile-nav-link i {
        font-size: 16px;
        min-width: 16px;
    }

    /* ===== CONTENT AREA ===== */
    .profile-content-area {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .profile-card {
        background: #fff;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .profile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
    }

    .profile-card-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
    }

    .profile-card-title i {
        color: var(--primary-color);
        font-size: 24px;
    }

    .profile-edit-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--primary-light);
        color: var(--primary-color);
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .profile-edit-btn:hover {
        background: var(--primary-color);
        color: #fff;
    }

    /* ===== FORM GRID ===== */
    .profile-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }

    .profile-form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .profile-form-group.full {
        grid-column: 1 / -1;
    }

    .profile-form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .profile-form-value {
        font-size: 15px;
        color: var(--text-primary);
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
        word-break: break-word;
    }

    .profile-form-input {
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 14px;
        color: var(--text-primary);
        transition: all 0.3s ease;
    }

    .profile-form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(43, 88, 255, 0.1);
    }

    .profile-form-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
        gap: 12px;
    }

    /* ===== BADGES ===== */
    .verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #e8f5e9;
        color: #2e7d32;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-badge.status-active {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-badge.status-inactive {
        background: #ffebee;
        color: #c62828;
    }

    .role-badge {
        display: inline-block;
        padding: 6px 12px;
        background: var(--primary-light);
        color: var(--primary-color);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    /* ===== BUTTONS ===== */
    .profile-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .profile-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .profile-btn-primary {
        background: var(--primary-color);
        color: #fff;
    }

    .profile-btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 88, 255, 0.3);
    }

    .profile-btn-secondary {
        background: var(--secondary-color);
        color: var(--text-primary);
    }

    .profile-btn-secondary:hover {
        background: #e0e2e6;
    }

    /* ===== TOGGLE SWITCH ===== */
    .toggle-switch {
        display: inline-flex;
        cursor: pointer;
    }

    .toggle-switch input {
        display: none;
    }

    .toggle-slider {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
        background: #ccc;
        border-radius: 12px;
        transition: 0.3s;
    }

    .toggle-slider::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        left: 2px;
        top: 2px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
    }

    .toggle-switch input:checked + .toggle-slider {
        background: var(--primary-color);
    }

    .toggle-switch input:checked + .toggle-slider::after {
        left: 26px;
    }

    /* ===== LOCATION SECTION ===== */
    .location-section {
        margin-bottom: 24px;
    }

    .location-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .location-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        .profile-container {
            grid-template-columns: 240px 1fr;
            gap: 20px;
            padding: 20px;
        }

        .profile-card {
            padding: 24px;
        }

        .profile-form-grid {
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .profile-container {
            grid-template-columns: 1fr;
            gap: 16px;
            padding: 16px;
        }

        .profile-sidebar {
            position: relative;
            top: 0;
            padding: 16px;
        }

        .profile-header {
            padding-bottom: 16px;
            margin-bottom: 16px;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
        }

        .profile-header-info h2 {
            font-size: 16px;
        }

        .profile-card {
            padding: 20px;
        }

        .profile-card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .profile-card-title {
            font-size: 18px;
        }

        .profile-form-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .profile-form-group.full {
            grid-column: 1;
        }

        .profile-actions {
            flex-direction: column;
        }

        .profile-btn {
            width: 100%;
            padding: 12px 16px;
        }

        .profile-nav-link {
            padding: 10px 12px;
            font-size: 13px;
        }

        .profile-nav-link span {
            display: none;
        }

        .profile-nav-link i {
            font-size: 18px;
        }

        .profile-nav-link.active span,
        .profile-nav-link:hover span {
            display: inline;
        }
    }

    @media (max-width: 480px) {
        .profile-container {
            padding: 12px;
            gap: 12px;
        }

        .profile-sidebar {
            padding: 12px;
        }

        .profile-card {
            padding: 16px;
        }

        .profile-card-title {
            font-size: 16px;
        }

        .profile-form-grid {
            gap: 12px;
        }

        .profile-form-label {
            font-size: 12px;
        }

        .profile-form-value {
            font-size: 14px;
        }

        .profile-btn {
            padding: 10px 12px;
            font-size: 13px;
        }

        .profile-nav-item {
            margin-bottom: 4px;
        }

        .profile-nav-link {
            padding: 8px 10px;
        }
    }
</style>
@endsection

