{{-- Reusable Sidebar Component --}}
@php
    $currentUser = auth()->user();
    $currentUser->load('details', 'role');
    $profilePhoto = $currentUser->details && $currentUser->details->photo 
        ? asset('storage/' . $currentUser->details->photo) 
        : asset('images/default-avatar.png');
    $userName = $currentUser->f_name . ' ' . $currentUser->l_name;
    $userRole = $currentUser->role->name ?? 'User';
    $currentRoute = Route::currentRouteName();
    
    // Determine if on account/profile page
    $isAccountPage = strpos($currentRoute, 'profile') !== false || strpos($currentRoute, 'account') !== false;
@endphp

<div class="sidebar" id="mainSidebar">
    <!-- Account Info Section (Top of Sidebar) -->
    <div class="sidebar-account-section">
        <div class="account-profile-container">
            <img src="{{ $profilePhoto }}" alt="{{ $userName }}" class="account-avatar">
            <div class="account-info">
                <div class="account-name">{{ $userName }}</div>
                <div class="account-role">{{ $userRole }}</div>
            </div>
        </div>
    </div>

    <!-- Logo and Title Section -->
    <div class="logo_content">
        <div class="logo_image_container">
            <img src="{{ asset('images/VCMSlogo.png') }}" alt="Logo" class="sidebar-logo">
        </div>   
        <div class="logo_title">
            <div class="logo_name" id="sidebarTitle">
                @if($isAccountPage)
                    Account Settings
                @else
                    @switch($currentRoute)
                        @case('dashboard')
                            Dashboard
                        @break
                        @case('clampings')
                            Clamping Management
                        @break
                        @case('payments')
                            Payment Records
                        @break
                        @case('users')
                            User Management
                        @break
                        @case('teams.index')
                        @case('teams.create')
                        @case('teams.show')
                        @case('teams.edit')
                            Teams Management
                        @break
                        @case('front-desk.dashboard')
                            Front Desk Dashboard
                        @break
                        @case('front-desk.violations')
                            Violations
                        @break
                        @case('front-desk.payments')
                            Payments
                        @break
                        @case('front-desk.archives')
                            Archives
                        @break
                        @case('admin.archives')
                            Archives
                        @break
                        @case('activity-logs')
                            Activity Logs
                        @break
                        @default
                            Vehicle Clamping Management System
                    @endswitch
                @endif
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <ul class="nav" id="sidebarNav">
        @if($isAccountPage)
            <!-- Account Settings Menu -->
            <li class="nav-item">
                <a href="@if(strtolower($userRole) === 'enforcer'){{ route('enforcer.profile') }}@else{{ route('admin.profile') }}@endif" class="nav-link {{ strpos($currentRoute, 'profile') !== false ? 'active' : '' }}">
                    <i class='fa-solid fa-user'></i>
                    <span class="link_name">My Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-section="security" onclick="handleSectionToggle(event, 'security')">
                    <i class='fa-solid fa-shield'></i>
                    <span class="link_name">Security</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-section="notifications" onclick="handleSectionToggle(event, 'notifications')">
                    <i class='fa-solid fa-bell'></i>
                    <span class="link_name">Notifications</span>
                </a>
            </li>
            @if(strtolower($userRole) === 'enforcer')
                <li class="nav-item">
                    <a href="{{ route('enforcer.location') }}" class="nav-link {{ $currentRoute === 'enforcer.location' ? 'active' : '' }}">
                        <i class='fa-solid fa-map-pin'></i>
                        <span class="link_name">GPS Location</span>
                    </a>
                </li>
            @endif
            @if($userRole === 'Admin')
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="teams" onclick="handleSectionToggle(event, 'teams')">
                        <i class='fa-solid fa-people-group'></i>
                        <span class="link_name">Teams</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="billing" onclick="handleSectionToggle(event, 'billing')">
                        <i class='fa-solid fa-credit-card'></i>
                        <span class="link_name">Billing</span>
                    </a>
                </li>
            @endif
            <li class="nav-item sidebar-divider"></li>
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link back-to-dashboard">
                    <i class='fa-solid fa-arrow-left'></i>
                    <span class="link_name">Back to Dashboard</span>
                </a>
            </li>
        @else
            <!-- Dynamic Main Menu Based on Role and Page -->
            @if($userRole === 'Admin')
                <!-- Admin Menu -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                        <i class='bx bx-grid-alt'></i>
                        <span class="link_name">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('clampings') }}" class="nav-link {{ $currentRoute === 'clampings' ? 'active' : '' }}">
                        <i class='bx bx-car'></i>
                        <span class="link_name">Clamping Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('payments') }}" class="nav-link {{ $currentRoute === 'payments' ? 'active' : '' }}">
                        <i class='bx bx-wallet'></i>
                        <span class="link_name">Payment Records</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users') }}" class="nav-link {{ $currentRoute === 'users' ? 'active' : '' }}">
                        <i class='bx bx-user'></i>
                        <span class="link_name">User Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('teams.index') }}" class="nav-link {{ strpos($currentRoute, 'teams') !== false ? 'active' : '' }}">
                        <i class='fa-solid fa-people-group'></i>
                        <span class="link_name">Teams Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.archives') }}" class="nav-link {{ $currentRoute === 'admin.archives' ? 'active' : '' }}">
                        <i class='bx bx-archive'></i>
                        <span class="link_name">Archives</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('activity-logs') }}" class="nav-link {{ $currentRoute === 'activity-logs' ? 'active' : '' }}">
                        <i class='bx bx-history'></i>
                        <span class="link_name">Activity Logs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('gps.dashboard') }}" class="nav-link {{ $currentRoute === 'gps.dashboard' ? 'active' : '' }}">
                        <i class='fa-solid fa-map-pin'></i>
                        <span class="link_name">GPS Tracking</span>
                    </a>
                </li>

            @elseif($userRole === 'Enforcer')
                <!-- Enforcer Menu -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                        <i class='bx bx-grid-alt'></i>
                        <span class="link_name">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('clampings') }}" class="nav-link {{ $currentRoute === 'clampings' ? 'active' : '' }}">
                        <i class='bx bx-car'></i>
                        <span class="link_name">My Clampings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('payments') }}" class="nav-link {{ $currentRoute === 'payments' ? 'active' : '' }}">
                        <i class='bx bx-wallet'></i>
                        <span class="link_name">Payment Records</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('enforcer.location') }}" class="nav-link {{ $currentRoute === 'enforcer.location' ? 'active' : '' }}">
                        <i class='fa-solid fa-map-pin'></i>
                        <span class="link_name">GPS Location</span>
                    </a>
                </li>

            @elseif($userRole === 'Front Desk')
                <!-- Front Desk Menu -->
                <li class="nav-item">
                    <a href="{{ route('front-desk.dashboard') }}" class="nav-link {{ $currentRoute === 'front-desk.dashboard' ? 'active' : '' }}">
                        <i class='bx bx-grid-alt'></i>
                        <span class="link_name">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('front-desk.violations') }}" class="nav-link {{ $currentRoute === 'front-desk.violations' ? 'active' : '' }}">
                        <i class='bx bx-car'></i>
                        <span class="link_name">Violations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('front-desk.payments') }}" class="nav-link {{ $currentRoute === 'front-desk.payments' ? 'active' : '' }}">
                        <i class='bx bx-wallet'></i>
                        <span class="link_name">Payments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('front-desk.archives') }}" class="nav-link {{ $currentRoute === 'front-desk.archives' ? 'active' : '' }}">
                        <i class='bx bx-archive'></i>
                        <span class="link_name">Archives</span>
                    </a>
                </li>

            @else
                <!-- Default/User Menu -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                        <i class='bx bx-grid-alt'></i>
                        <span class="link_name">Dashboard</span>
                    </a>
                </li>
            @endif
        @endif

        <!-- Always visible menu items -->
        <li class="nav-item sidebar-divider"></li>
        <li class="nav-item">
            <a href="{{ route('admin.profile') }}" class="nav-link {{ strpos($currentRoute, 'profile') !== false ? 'active' : '' }}">
                <i class='fa-solid fa-cog'></i>
                <span class="link_name">Settings</span>
            </a>
        </li>
    </ul>

    <!-- Bottom Logout Section -->
    <div class="sidebar-bottom">
        <a href="#" id="logoutLinkSidebar" class="logout-link">
            <i class='bx bx-log-out'></i>
            <span class="link_name">Logout</span>
        </a>
    </div>
</div>

<style>
    /* Sidebar Account Section */
    .sidebar-account-section {
        padding: 20px 15px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border-bottom: 2px solid #004085;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .account-profile-container {
        display: flex;
        align-items: center;
        gap: 15px;
        width: 100%;
    }

    .account-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .account-avatar:hover {
        transform: scale(1.1);
    }

    .account-info {
        flex: 1;
        min-width: 0;
        color: white;
    }

    .account-name {
        font-weight: 700;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .account-role {
        font-size: 11px;
        opacity: 0.9;
        font-weight: 500;
        text-transform: capitalize;
    }

    /* Logo and Title */
    .logo_content {
        padding: 20px 0;
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 15px;
        text-align: center;
    }

    .logo_image_container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 5px 0 10px 0;
    }

    .sidebar-logo {
        width: 600px;
        height: auto;
        object-fit: contain;
        max-width: 100%;
    }

    .logo_title {
        margin-top: 10px;
    }

    .logo_name {
        font-size: 20px;
        font-weight: 700;
        color: #2b35af;
        line-height: 1.3;
        margin-bottom: 2px;
        transition: all 0.3s ease;
    }

    /* Navigation Items */
    .nav {
        list-style: none;
        padding: 0 10px;
        margin: 0;
    }

    .nav-item {
        margin-bottom: 8px;
    }

    .nav-item.sidebar-divider {
        height: 1px;
        background: #e0e0e0;
        margin: 15px 0;
        cursor: default;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 18px;
        color: #666;
        text-decoration: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        background: #f0f5ff;
        color: #007bff;
        transform: translateX(5px);
    }

    .nav-link.active {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .nav-link.active i {
        color: white;
    }

    .nav-link i {
        font-size: 18px;
        color: inherit;
        transition: all 0.3s ease;
    }

    .nav-link.back-to-dashboard {
        background: #f9f9f9;
        color: #666;
        margin-top: 15px;
        border: 2px solid #e0e0e0;
    }

    .nav-link.back-to-dashboard:hover {
        background: #f0f0f0;
        border-color: #007bff;
    }

    /* Bottom Logout Section */
    .sidebar-bottom {
        position: absolute;
        bottom: 20px;
        width: 100%;
        padding: 0 10px;
        box-sizing: border-box;
    }

    .logout-link {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 18px;
        color: #dc3545;
        text-decoration: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
        background: #fff3f3;
        border: 2px solid #ffcccc;
    }

    .logout-link:hover {
        background: #ffcccc;
        color: #c82333;
        transform: translateX(5px);
    }

    .logout-link i {
        font-size: 18px;
    }

    @media (max-width: 768px) {
        .account-profile-container {
            padding: 0 5px;
        }

        .account-avatar {
            width: 40px;
            height: 40px;
        }

        .account-name {
            font-size: 12px;
        }

        .account-role {
            font-size: 10px;
        }

        .logo_name {
            font-size: 16px;
        }

        .nav-link {
            padding: 10px 14px;
            font-size: 13px;
            gap: 12px;
        }
    }
</style>
