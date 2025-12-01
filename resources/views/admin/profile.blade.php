@extends('layouts.app')

@section('title', 'Account Settings')

@php
    // Determine the profile route based on user role
    $userRole = $user->role->name ?? 'Admin';
    
    // Try to use the current route name to determine role context
    $currentRoute = Route::currentRouteName();
    
    // Map current route to determine which profile context we're in
    if (str_contains($currentRoute, 'front-desk')) {
        $profileEditRoute = 'front-desk.profile.edit';
        $profileUpdateRoute = 'front-desk.profile.update';
        $dashboardRoute = 'front-desk.dashboard';
    } elseif (str_contains($currentRoute, 'enforcer') || $userRole === 'Enforcer') {
        $profileEditRoute = 'enforcer.profile.edit';
        $profileUpdateRoute = 'enforcer.profile.update';
        $dashboardRoute = 'enforcer.dashboard';
    } else {
        $profileEditRoute = 'admin.profile.edit';
        $profileUpdateRoute = 'admin.profile.update';
        $dashboardRoute = 'dashboard';
    }
@endphp

@section('content')
<div class="account-settings-wrapper">
    <!-- Sidebar Navigation - HIDDEN (Using main sidebar) -->
    <div class="settings-sidebar" style="display: none;">
        <h3 class="sidebar-title">Account Settings</h3>
        <nav class="settings-nav">
            <a href="#profile" class="nav-item active" data-section="profile">
                <i class="fa-solid fa-user"></i>
                <span>My Profile</span>
            </a>
            <a href="#security" class="nav-item" data-section="security">
                <i class="fa-solid fa-shield"></i>
                <span>Security</span>
            </a>
            @if(strtolower($user->role->name ?? '') === 'enforcer')
            <a href="{{ route('enforcer.location') }}" class="nav-item">
                <i class="fa-solid fa-map-pin"></i>
                <span>GPS Location</span>
            </a>
            @endif
            <a href="#notifications" class="nav-item" data-section="notifications">
                <i class="fa-solid fa-people-group"></i>
                <span>Teams</span>
            </a>
            <a href="#notifications" class="nav-item" data-section="notifications">
                <i class="fa-solid fa-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="#billing" class="nav-item" data-section="billing">
                <i class="fa-solid fa-credit-card"></i>
                <span>Billing</span>
            </a>
            <a href="#dangerzone" class="nav-item danger" data-section="dangerzone">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Delete Account</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="settings-content">
        <!-- Pending Account Notice -->
        @php
            $userStatus = strtolower($user->status->status ?? 'active');
            $isPending = $userStatus === 'pending';
        @endphp
        
        @if($isPending)
            <div class="pending-account-banner">
                <i class="fas fa-clock"></i>
                <div class="pending-account-banner-content">
                    <h4>Account Pending Approval</h4>
                    <p>Your account is currently awaiting administrator approval. In the meantime, you have limited access:</p>
                    <ul>
                        <li>View and edit your profile information</li>
                        <li>Manage your account settings and security</li>
                        <li>Other features will be available once approved</li>
                    </ul>
                </div>
            </div>
        @endif

        <!-- Profile Section -->
        <div class="settings-section" id="profile-section">
            <div class="section-header">
                <h2>My Profile</h2>
            </div>

            <!-- Profile Header -->
            <div class="profile-card">
                <div class="profile-header-content">
                    <div class="profile-avatar">
                        <img src="{{ $user->details && $user->details->photo ? asset('storage/' . $user->details->photo) : asset('images/default-avatar.png') }}" alt="Profile">
                        <span class="role-badge">{{ $user->role->name ?? 'Administrator' }}</span>
                    </div>
                    <div class="profile-info-section">
                        <h3>{{ $user->f_name }} {{ $user->l_name }}</h3>
                        <p class="location">{{ $user->details && $user->details->address ? $user->details->address : 'Location not specified' }}</p>
                    </div>
                    <a href="{{ route($profileEditRoute) }}" class="btn-edit">
                        <i class="fa-solid fa-pen"></i>
                        Edit
                    </a>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="info-section">
                <div class="section-title">
                    <h4>Personal Information</h4>
                    <a href="{{ route($profileEditRoute) }}" class="link-edit">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>First Name</label>
                        <p>{{ $user->f_name }}</p>
                    </div>
                    <div class="info-item">
                        <label>Last Name</label>
                        <p>{{ $user->l_name }}</p>
                    </div>
                    <div class="info-item">
                        <label>Email address</label>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="info-item">
                        <label>Phone</label>
                        <p>{{ $user->phone ?? 'Not specified' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Bio</label>
                        <p>{{ $user->role->name ?? 'Administrator' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Status</label>
                        <p>
                            <span class="status-badge active">{{ $user->status->status ?? 'Active' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="info-section">
                <div class="section-title">
                    <h4>Address</h4>
                    <a href="{{ route($profileEditRoute) }}" class="link-edit">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Country</label>
                        <p>{{ $user->details && $user->details->country ? $user->details->country : 'Not specified' }}</p>
                    </div>
                    <div class="info-item">
                        <label>City/State</label>
                        <p>{{ $user->details && $user->details->city ? $user->details->city : 'Not specified' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Postal Code</label>
                        <p>{{ $user->details && $user->details->postal_code ? $user->details->postal_code : 'Not specified' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Street Address</label>
                        <p>{{ $user->details && $user->details->address ? $user->details->address : 'Not specified' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Section -->
        <div class="settings-section" id="security-section" style="display: none;">
            <div class="section-header">
                <h2>Security</h2>
            </div>
            <div style="background: #f9f9f9; border-radius: 8px; padding: 24px;">
                <!-- Device Manager Card -->
                <div style="margin-bottom: 24px;">
                    <h4 style="margin: 0 0 12px 0; color: #333; font-size: 16px; font-weight: 600;">
                        <i class="fa-solid fa-mobile" style="margin-right: 8px; color: #2b58ff;"></i>
                        Device Manager
                    </h4>
                    <p style="color: #666; font-size: 14px; margin: 0 0 12px 0;">View and manage all devices logged into your account.</p>
                    <a href="{{ route('devices.index') }}" style="display: inline-block; padding: 10px 20px; background: #2b58ff; color: white; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.3s;">
                        <i class="fa-solid fa-arrow-right" style="margin-right: 6px;"></i>Manage Devices
                    </a>
                </div>
                <div style="padding-top: 24px; border-top: 1px solid #eee;">
                    <h4 style="margin: 0 0 12px 0; color: #333; font-size: 16px; font-weight: 600;">Change Password</h4>
                    <p style="color: #666; font-size: 14px; margin: 0;">Update your password to keep your account secure.</p>
                    <button onclick="alert('Change password feature coming soon')" style="margin-top: 12px; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Change Password</button>
                </div>
                <div style="padding-top: 24px; border-top: 1px solid #eee;">
                    <h4 style="margin: 0 0 12px 0; color: #333; font-size: 16px; font-weight: 600;">Two-Factor Authentication</h4>
                    <p style="color: #666; font-size: 14px; margin: 0;">Add an extra layer of security to your account.</p>
                    <button onclick="alert('2FA feature coming soon')" style="margin-top: 12px; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Enable 2FA</button>
                </div>

                <!-- Location Toggle Section - Only for Enforcers -->
                @if(strtolower($user->role->name ?? '') === 'enforcer')
                <div style="padding-top: 24px; border-top: 1px solid #eee;">
                    <h4 style="margin: 0 0 12px 0; color: #333; font-size: 16px; font-weight: 600;">
                        <i class="fa-solid fa-location-dot" style="margin-right: 8px; color: #2b58ff;"></i>
                        Location Tracking
                    </h4>
                    <p style="color: #666; font-size: 14px; margin: 0 0 16px 0;">Enable or disable real-time GPS location tracking on your account. Admins can see your location when tracking is enabled.</p>
                    
                    <!-- Location Toggle Control -->
                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0;">
                            <input type="checkbox" id="locationToggle" style="width: 18px; height: 18px; cursor: pointer;" />
                            <span style="color: #666; font-size: 14px;">Enable location tracking</span>
                        </label>
                        <span id="locationStatus" style="display: inline-block; padding: 4px 12px; background: #f0f0f0; border-radius: 20px; font-size: 12px; color: #666; font-weight: 600;">Status: OFF</span>
                    </div>
                    
                    <p style="color: #999; font-size: 12px; margin: 12px 0 0 0;">When enabled, your real-time GPS location will be visible to authorized administrators during working hours.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Teams Section -->
        <div class="settings-section" id="teams-section" style="display: none;">
            <div class="section-header">
                <h2>Teams Management</h2>
            </div>
            <div style="background: #f9f9f9; border-radius: 8px; padding: 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <!-- View Teams Card -->
                    <div style="background: white; border-radius: 8px; padding: 20px; text-align: center; border: 1px solid #e9ecef; transition: all 0.3s;">
                        <i style="font-size: 36px; color: #2b58ff; display: block; margin-bottom: 12px;" class="fa-solid fa-list"></i>
                        <h5 style="margin: 0 0 8px 0; color: #333; font-size: 15px; font-weight: 600;">View All Teams</h5>
                        <p style="color: #666; font-size: 13px; margin: 0 0 12px 0;">Manage and view all teams</p>
                        <a href="{{ route('teams.index') }}" style="display: inline-block; padding: 10px 20px; background: #f0f0f0; color: #2b58ff; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s; font-size: 13px;">
                            <i class="fa-solid fa-arrow-right" style="margin-right: 6px;"></i>View Teams
                        </a>
                    </div>
                    <!-- Create Team Card -->
                    <div style="background: white; border-radius: 8px; padding: 20px; text-align: center; border: 1px solid #e3f2fd; transition: all 0.3s;">
                        <i style="font-size: 36px; color: #16a34a; display: block; margin-bottom: 12px;" class="fa-solid fa-plus-circle"></i>
                        <h5 style="margin: 0 0 8px 0; color: #333; font-size: 15px; font-weight: 600;">Create New Team</h5>
                        <p style="color: #666; font-size: 13px; margin: 0 0 12px 0;">Assign enforcers to new teams</p>
                        <a href="{{ route('teams.create') }}" style="display: inline-block; padding: 10px 20px; background: #16a34a; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s; font-size: 13px;">
                            <i class="fa-solid fa-plus" style="margin-right: 6px;"></i>Create Team
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Section -->
        <div class="settings-section" id="notifications-section" style="display: none;">
            <div class="section-header">
                <h2>Notifications</h2>
            </div>
            <div style="background: #f9f9f9; border-radius: 8px; padding: 24px;">
                <div style="margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h4 style="margin: 0 0 4px 0; color: #333; font-size: 15px; font-weight: 600;">Email Notifications</h4>
                        <p style="color: #666; font-size: 13px; margin: 0;">Receive notifications via email</p>
                    </div>
                    <label style="position: relative; display: inline-block; width: 50px; height: 24px;">
                        <input type="checkbox" checked style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; border-radius: 24px; transition: 0.3s; background-color: #2b58ff;"></span>
                    </label>
                </div>
                <div style="padding-top: 16px; border-top: 1px solid #eee; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h4 style="margin: 0 0 4px 0; color: #333; font-size: 15px; font-weight: 600;">SMS Notifications</h4>
                        <p style="color: #666; font-size: 13px; margin: 0;">Receive notifications via SMS</p>
                    </div>
                    <label style="position: relative; display: inline-block; width: 50px; height: 24px;">
                        <input type="checkbox" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; border-radius: 24px; transition: 0.3s;"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Billing Section -->
        <div class="settings-section" id="billing-section" style="display: none;">
            <div class="section-header">
                <h2>Billing</h2>
            </div>
            <div style="background: #f9f9f9; border-radius: 8px; padding: 24px;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div>
                        <p style="color: #999; font-size: 12px; text-transform: uppercase; font-weight: 700; margin: 0 0 8px 0;">Plan</p>
                        <p style="color: #333; font-size: 16px; font-weight: 700; margin: 0;">Enterprise</p>
                    </div>
                    <div>
                        <p style="color: #999; font-size: 12px; text-transform: uppercase; font-weight: 700; margin: 0 0 8px 0;">Billing Cycle</p>
                        <p style="color: #333; font-size: 16px; font-weight: 700; margin: 0;">Monthly</p>
                    </div>
                    <div>
                        <p style="color: #999; font-size: 12px; text-transform: uppercase; font-weight: 700; margin: 0 0 8px 0;">Next Billing Date</p>
                        <p style="color: #333; font-size: 16px; font-weight: 700; margin: 0;">{{ now()->addMonth()->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p style="color: #999; font-size: 12px; text-transform: uppercase; font-weight: 700; margin: 0 0 8px 0;">Payment Method</p>
                        <p style="color: #333; font-size: 16px; font-weight: 700; margin: 0;">Credit Card</p>
                    </div>
                </div>
                <button onclick="alert('Billing management coming soon')" style="padding: 10px 20px; background: #2b58ff; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Update Billing Info</button>
            </div>
        </div>

        <!-- Back Button -->
        <div class="action-buttons">
            <a href="{{ route($dashboardRoute) }}" class="btn-secondary">← Back to Dashboard</a>
        </div>
    </div>
</div>

<style>
    .account-settings-wrapper {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0;
        width: 100%;
        padding: 0;
        background: transparent;
    }

    /* Sidebar */
    .settings-sidebar {
        background: #fff;
        border-radius: 12px;
        padding: 28px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        height: fit-content;
        position: sticky;
        top: 32px;
    }

    .sidebar-title {
        margin: 0 0 24px 0;
        font-size: 15px;
        font-weight: 700;
        color: #333;
        letter-spacing: 0.3px;
    }

    .settings-nav {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        color: #666;
        text-decoration: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s;
        cursor: pointer;
    }

    .nav-item i {
        width: 18px;
        text-align: center;
        font-size: 15px;
    }

    .nav-item:hover {
        background: #f5f5f5;
        color: #333;
        transform: translateX(4px);
    }

    .nav-item.active {
        background: #e3f2fd;
        color: #2b58ff;
        font-weight: 600;
        border-left: 3px solid #2b58ff;
        padding-left: 13px;
    }

    .nav-item.danger {
        color: #dc3545;
    }

    .nav-item.danger:hover {
        background: rgba(220, 53, 69, 0.1);
    }

    /* Main Content */
    .settings-content {
        display: grid;
        gap: 24px;
        padding: 32px 24px;
        width: 100%;
        box-sizing: border-box;
    }

    .settings-section {
        background: #fff;
        border-radius: 12px;
        padding: 32px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }

    .section-header {
        margin-bottom: 28px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }

    /* Profile Card */
    .profile-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 32px;
        color: #fff;
        margin-bottom: 32px;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.25);
        width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }

    .profile-header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        width: 100%;
        flex-wrap: nowrap;
    }

    .profile-avatar {
        position: relative;
        flex-shrink: 0;
        min-width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 120px;
    }

    .profile-avatar img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid #fff;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        display: block;
        flex-shrink: 0;
    }

    .role-badge {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        background: #fff;
        color: #667eea;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        white-space: nowrap;
    }

    .profile-info-section {
        flex: 1;
        min-width: 0;
    }

    .profile-info-section h3 {
        margin: 0 0 8px 0;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: -0.5px;
        word-wrap: break-word;
    }

    .profile-info-section .location {
        margin: 0;
        font-size: 15px;
        opacity: 0.95;
        word-wrap: break-word;
    }

    .btn-edit {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(255,255,255,0.2);
        color: #fff;
        padding: 12px 22px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s;
        border: 1px solid rgba(255,255,255,0.3);
        white-space: nowrap;
        flex-shrink: 0;
        min-height: 44px;
    }

    .btn-edit:hover {
        background: rgba(255,255,255,0.3);
        border-color: rgba(255,255,255,0.5);
        transform: translateY(-2px);
    }

    /* Info Section */
    .info-section {
        margin-bottom: 32px;
    }

    .info-section:last-of-type {
        margin-bottom: 0;
    }

    .section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1.5px solid #f0f0f0;
    }

    .section-title h4 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #333;
        letter-spacing: -0.2px;
    }

    .link-edit {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #2b58ff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .link-edit:hover {
        color: #1e42cc;
        gap: 10px;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 32px;
        width: 100%;
        box-sizing: border-box;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .info-item label {
        font-size: 11px;
        font-weight: 800;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .info-item p {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        line-height: 1.5;
    }

    .status-badge {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        width: fit-content;
    }

    .status-badge.active {
        background: #e3f2fd;
        color: #1976d2;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 32px;
        border-top: 2px solid #f0f0f0;
    }

    .btn-secondary {
        padding: 12px 24px;
        background: #f0f0f0;
        color: #333;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: #e0e0e0;
        transform: translateY(-2px);
    }

    @media (max-width: 1024px) {
        .account-settings-wrapper {
            grid-template-columns: 180px 1fr;
            gap: 24px;
            padding: 24px 20px;
        }

        .info-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .settings-section {
            padding: 24px;
        }

        .profile-card {
            padding: 24px;
        }
    }

    @media (max-width: 768px) {
        .account-settings-wrapper {
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 20px 16px;
            min-height: auto;
        }

        .settings-sidebar {
            position: static;
            padding: 20px 16px;
            display: none !important;
        }

        .settings-nav {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 8px;
        }

        .nav-item {
            flex: 1;
            min-width: 90px;
            padding: 10px 12px;
            font-size: 13px;
        }

        .nav-item.active {
            border-left: 0;
            padding-left: 12px;
        }

        .nav-item span {
            display: none;
        }

        .nav-item i {
            margin: 0 auto;
        }

        .info-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .profile-header-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .profile-avatar {
            min-width: 110px;
            width: 110px;
            height: 110px;
            margin: 0 auto;
        }

        .profile-avatar img {
            width: 110px;
            height: 110px;
        }

        .role-badge {
            font-size: 10px;
            padding: 6px 12px;
        }

        .profile-info-section {
            width: 100%;
            text-align: center;
        }

        .profile-info-section h3 {
            font-size: 22px;
        }

        .profile-info-section .location {
            font-size: 14px;
        }

        .btn-edit {
            width: 100%;
            justify-content: center;
            padding: 12px 16px;
        }

        .settings-section {
            padding: 20px 16px;
        }

        .profile-card {
            padding: 20px 16px;
        }

        .section-header h2 {
            font-size: 20px;
        }

        .section-title {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .link-edit {
            align-self: flex-start;
        }
    }

    @media (max-width: 480px) {
        .account-settings-wrapper {
            grid-template-columns: 1fr;
            gap: 16px;
            padding: 12px;
        }

        .info-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .profile-avatar {
            min-width: 100px;
            width: 100px;
            height: 100px;
        }

        .profile-avatar img {
            width: 100px;
            height: 100px;
        }

        .profile-info-section h3 {
            font-size: 20px;
            margin-bottom: 4px;
        }

        .profile-info-section .location {
            font-size: 13px;
        }

        .profile-header-content {
            gap: 12px;
        }

        .btn-edit {
            width: 100%;
            padding: 10px 12px;
            font-size: 13px;
            min-height: 40px;
        }

        .settings-section {
            padding: 14px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .profile-card {
            padding: 14px 12px;
            margin-bottom: 16px;
        }

        .section-header {
            margin-bottom: 16px;
            padding-bottom: 12px;
        }

        .section-header h2 {
            font-size: 18px;
        }

        .section-title h4 {
            font-size: 15px;
        }

        .info-item label {
            font-size: 10px;
        }

        .info-item p {
            font-size: 14px;
        }

        .action-buttons {
            gap: 8px;
            flex-direction: column;
            margin-top: 16px;
            padding-top: 16px;
        }

        .btn-secondary {
            width: 100%;
            justify-content: center;
            padding: 10px 12px;
            font-size: 13px;
        }

        .settings-content {
            padding: 12px;
            gap: 12px;
        }
    }

    @media (max-width: 360px) {
        .profile-avatar {
            min-width: 90px;
            width: 90px;
            height: 90px;
        }

        .profile-avatar img {
            width: 90px;
            height: 90px;
        }

        .profile-info-section h3 {
            font-size: 18px;
        }

        .section-header h2 {
            font-size: 16px;
        }

        .settings-section {
            padding: 10px;
            margin-bottom: 8px;
        }

        .profile-card {
            padding: 10px;
        }

        .info-item label {
            font-size: 9px;
        }

        .info-item p {
            font-size: 12px;
        }

        .btn-edit {
            padding: 8px 10px;
            font-size: 12px;
        }

        .settings-content {
            padding: 8px;
            gap: 8px;
        }
    }

    /* Enhanced mobile responsiveness for all screen sizes */
    @media (max-width: 600px) {
        .account-settings-wrapper {
            margin: 0;
            padding: 0;
        }

        .settings-sidebar {
            display: none !important;
        }

        .settings-content {
            max-width: 100%;
            margin: 0;
            padding: 12px;
        }

        .profile-header-content {
            flex-direction: column;
            text-align: center;
        }

        .profile-avatar {
            margin: 0 auto;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .profile-card {
            padding: 12px;
        }

        .settings-section {
            padding: 12px;
        }
    }
</style>

<script>
    // Initialize account settings sections
    document.addEventListener('DOMContentLoaded', function() {
        // Show only profile section on page load
        const sections = document.querySelectorAll('.settings-section');
        sections.forEach((section, index) => {
            if (index === 0) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });

        // Initialize location toggle
        const locationToggle = document.getElementById('locationToggle');
        const locationStatus = document.getElementById('locationStatus');
        
        if (locationToggle) {
            // Load saved state from localStorage
            const savedState = localStorage.getItem('locationTrackingEnabled');
            if (savedState === 'true') {
                locationToggle.checked = true;
                updateLocationStatus(true);
            }

            // Listen for toggle changes
            locationToggle.addEventListener('change', function() {
                const isEnabled = this.checked;
                localStorage.setItem('locationTrackingEnabled', isEnabled);
                updateLocationStatus(isEnabled);
                
                // Optional: Send to server if you have a backend endpoint
                console.log('Location tracking:', isEnabled ? 'ENABLED' : 'DISABLED');
            });
        }

        function updateLocationStatus(enabled) {
            if (enabled) {
                locationStatus.textContent = 'Status: ON';
                locationStatus.style.background = '#d4edda';
                locationStatus.style.color = '#155724';
            } else {
                locationStatus.textContent = 'Status: OFF';
                locationStatus.style.background = '#f0f0f0';
                locationStatus.style.color = '#666';
            }
        }
    });

    // Handle section toggle from sidebar
    document.addEventListener('toggleSection', function(event) {
        const section = event.detail.section;
        const sectionId = section + '-section';
        const sectionElement = document.getElementById(sectionId);
        
        if (sectionElement) {
            // Hide all sections
            document.querySelectorAll('.settings-section').forEach(sec => {
                sec.style.display = 'none';
            });
            
            // Show the selected section
            sectionElement.style.display = 'block';
            
            // Scroll to section if needed
            sectionElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
</script>

@endsection

