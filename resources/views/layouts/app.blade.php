<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Clamping Dashboard')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/VCMSlogo.png') }}">
    <link rel="stylesheet" href="{{ asset('styles/users.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/payments.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/clamping.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/style.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/dialog.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/overlay.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/teams-show.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/pending-account.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body data-user-status="{{ strtolower(auth()->user()->status->status ?? 'active') }}">
    <div class="sidebar">
        @php
            $currentUser = auth()->user();
            $currentUser->load('details', 'role');
            $profilePhoto = $currentUser->details && $currentUser->details->photo 
                ? asset('storage/' . $currentUser->details->photo) 
                : asset('images/default-avatar.png');
            $userName = $currentUser->f_name . ' ' . $currentUser->l_name;
            $userRole = $currentUser->role->name ?? 'User';
            $currentRoute = Route::currentRouteName();
            $isAccountPage = strpos($currentRoute, 'profile') !== false;
            $isEnforcerRole = strtolower($userRole) === 'enforcer';
            
            // Determine correct dashboard route based on role
            $dashboardRoute = match($userRole) {
                'Front Desk' => 'front-desk.dashboard',
                'Enforcer' => 'enforcer.dashboard',
                default => 'dashboard',
            };
        @endphp

        <!-- Account Info Section at Top -->
        <div class="sidebar-account-section">
            <img src="{{ $profilePhoto }}" alt="{{ $userName }}" class="account-avatar">
            <div class="account-info">
                <div class="account-name">{{ $userName }}</div>
                <div class="account-role">{{ $userRole }}</div>
            </div>
        </div>
        
        <!-- Logo and Heading Section -->
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
                                Vehicle Clamping System
                            @break
                            @case('clampings')
                                Vehicle Clamping Management System
                            @break
                            @case('payments')
                                Payments
                            @break
                            @case('users')
                                Vehicle Clamping Management System
                            @break
                            @case('teams.index')
                            @case('teams.create')
                            @case('teams.show')
                            @case('teams.edit')
                                Vehicle Clamping Management System
                            @break
                            @case('admin.archives')
                                Archives
                            @break
                            @case('activity-logs')
                                Vehicle Clamping Management System
                            @break
                            @default
                                Vehicle Clamping Management System
                        @endswitch
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav">
            @if($isAccountPage)
                <!-- Account Settings Menu -->
                <li>
                    <a href="{{ route('admin.profile') }}" class="nav-link {{ strpos($currentRoute, 'profile') !== false ? 'active' : '' }}">
                        <i class='fa-solid fa-user'></i>
                        <span class="link_name">My Profile</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link" onclick="toggleAccountSection(event, 'security')">
                        <i class='fa-solid fa-shield'></i>
                        <span class="link_name">Security</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link" onclick="toggleAccountSection(event, 'notifications')">
                        <i class='fa-solid fa-bell'></i>
                        <span class="link_name">Notifications</span>
                    </a>
                </li>
                @if($userRole === 'Admin')
                    <li>
                        <a href="#" class="nav-link" onclick="toggleAccountSection(event, 'teams')">
                            <i class='fa-solid fa-people-group'></i>
                            <span class="link_name">Teams</span>
                        </a>
                    </li>
                @endif
                <li style="border-top: 1px solid #e0e0e0; margin-top: 15px; padding-top: 15px;">
                    <a href="{{ route($dashboardRoute) }}" class="nav-link">
                        <i class='fa-solid fa-arrow-left'></i>
                        <span class="link_name">Back to Dashboard</span>
                    </a>
                </li>
            @else
                <!-- Main Dashboard Menu -->
                @if($userRole === 'Admin')
                    <li>
                        <a href="{{ route('dashboard') }}" class="nav-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                            <i class='bx bx-grid-alt'></i>
                            <span class="link_name">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('clampings') }}" class="nav-link {{ $currentRoute === 'clampings' ? 'active' : '' }}">
                            <i class='bx bx-car'></i>
                            <span class="link_name">Clamping Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payments') }}" class="nav-link {{ $currentRoute === 'payments' ? 'active' : '' }}">
                            <i class='bx bx-wallet'></i>
                            <span class="link_name">Payments</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users') }}" class="nav-link {{ $currentRoute === 'users' ? 'active' : '' }}">
                            <i class='bx bx-user'></i>
                            <span class="link_name">User Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('teams.index') }}" class="nav-link {{ strpos($currentRoute, 'teams') !== false ? 'active' : '' }}">
                            <i class='fa-solid fa-people-group'></i>
                            <span class="link_name">Teams Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('analytics.dashboard') }}" class="nav-link {{ $currentRoute === 'analytics.dashboard' ? 'active' : '' }}">
                            <i class='bx bx-bar-chart'></i>
                            <span class="link_name">Analytics</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('zones.index') }}" class="nav-link {{ strpos($currentRoute, 'zones') !== false ? 'active' : '' }}">
                            <i class='bx bx-map'></i>
                            <span class="link_name">Parking Zones</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tracking.index') }}" class="nav-link {{ strpos($currentRoute, 'tracking') !== false ? 'active' : '' }}">
                            <i class='bx bx-map-pin'></i>
                            <span class="link_name">Enforcer Tracking</span>
                        </a>
                    </li>
                @elseif($userRole === 'Front Desk')
                    <li>
                        <a href="{{ route('dashboard') }}" class="nav-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                            <i class='bx bx-grid-alt'></i>
                            <span class="link_name">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('clampings') }}" class="nav-link {{ $currentRoute === 'clampings' ? 'active' : '' }}">
                            <i class='bx bx-car'></i>
                            <span class="link_name">Violations</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payments') }}" class="nav-link {{ $currentRoute === 'payments' ? 'active' : '' }}">
                            <i class='bx bx-wallet'></i>
                            <span class="link_name">Payments</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('notifications.index') }}" class="nav-link {{ strpos($currentRoute, 'notifications') !== false ? 'active' : '' }}">
                            <i class='bx bx-bell'></i>
                            <span class="link_name">Notifications</span>
                            <span id="notificationBadgeSidebar" class="badge-sidebar" style="display: none; background: #dc3545; color: white; border-radius: 12px; padding: 2px 6px; font-size: 11px; font-weight: 600; margin-left: auto;">0</span>
                        </a>
                    </li>
                @elseif($userRole === 'Enforcer')
                    <li>
                        <a href="{{ route('dashboard') }}" class="nav-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                            <i class='bx bx-grid-alt'></i>
                            <span class="link_name">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('clampings') }}" class="nav-link {{ $currentRoute === 'clampings' ? 'active' : '' }}">
                            <i class='bx bx-car'></i>
                            <span class="link_name">My Clampings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payments') }}" class="nav-link {{ $currentRoute === 'payments' ? 'active' : '' }}">
                            <i class='bx bx-wallet'></i>
                            <span class="link_name">Payments</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('enforcer.location') }}" class="nav-link {{ $currentRoute === 'enforcer.location' ? 'active' : '' }}">
                            <i class='bx bx-navigation'></i>
                            <span class="link_name">GPS Tracker</span>
                        </a>
                    </li>
                @else
                    <li>
                        <a href="{{ route('dashboard') }}" class="nav-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                            <i class='bx bx-grid-alt'></i>
                            <span class="link_name">Dashboard</span>
                        </a>
                    </li>
                @endif
            @endif

            <!-- Always visible: Settings link -->
            
            <li>
                <a href="#" id="logoutLink" class="nav-link logout-link" onclick="logout(event)">
                    <i class='bx bx-log-out'></i>
                    <span class="link_name">Logout</span>
                </a>
            </li>
        </ul>

    </div>

    <!-- Main Content -->
    <div class="home_content">
        <!-- Top bar -->
        <div class="topbar">
            <button id="sidebarToggle" class="sidebar-toggle-btn" style="display: none; background: none; border: none; font-size: 24px; cursor: pointer; color: white; padding: 5px;">
                <i class='bx bx-menu'></i>
            </button>
            <div class="text" id="topbarTitle">
                @php
                    $currentRoute = Route::currentRouteName();
                    if (strpos($currentRoute, 'profile') !== false) {
                        echo 'Account Settings';
                    } else {
                        $pageTitle = match($currentRoute) {
                            'dashboard' => 'Dashboard',
                            'clampings' => 'Clamping Management',
                            'payments' => 'Payments',
                            'users' => 'User Management',
                            'teams.index', 'teams.create', 'teams.show', 'teams.edit' => 'Teams Management',
                            'admin.archives' => 'Archives',
                            'activity-logs' => 'Activity Logs',
                            default => 'Vehicle Clamping Management System'
                        };
                        echo $pageTitle;
                    }
                @endphp
            </div>
            <div class="profile" style="position: relative;">
                @php
                    $currentUser = auth()->user();
                    $currentUser->load('details');
                    $profilePhoto = $currentUser->details && $currentUser->details->photo 
                        ? asset('storage/' . $currentUser->details->photo) 
                        : asset('images/default-avatar.png');
                    $userName = $currentUser->f_name . ' ' . $currentUser->l_name;
                @endphp
                
                <!-- Profile Avatar and Dropdown -->
                <div class="profile-dropdown-container" id="profileDropdownToggle">
                    <div class="profile-info-wrapper">
                        <img src="{{ $profilePhoto }}" alt="Profile" class="profile-avatar">
                        <div class="profile-text-wrapper">
                            <span class="profile-name">{{ $userName }}</span>
                            <span class="profile-role">{{ $currentUser->role->name ?? 'Admin' }}</span>
                        </div>
                    </div>
                    <i class='bx bx-chevron-down' id="dropdownChevron"></i>
                </div>

                <!-- Profile Dropdown Menu -->
                <div id="profileDropdown" class="profile-dropdown" style="display: none;">
                    <div class="dropdown-header">
                        <img src="{{ $profilePhoto }}" alt="Profile" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #007bff;">
                        <div>
                            <div style="font-weight: 600; color: #333; font-size: 14px;">{{ $userName }}</div>
                            <div style="font-size: 12px; color: #666;">{{ $currentUser->email }}</div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    @php
                        $profileRoute = match($userRole) {
                            'Front Desk' => 'front-desk.profile',
                            'Enforcer' => 'enforcer.profile',
                            default => 'admin.profile',
                        };
                    @endphp
                    <a href="{{ route($profileRoute) }}" class="dropdown-item">
                        <i class='bx bx-user'></i>
                        <span>My Profile</span>
                    </a>
                    <a href="#" id="notificationsDropdownLink" class="dropdown-item">
                        <i class='bx bx-bell'></i>
                        <span>Notifications</span>
                        <span id="notificationBadgeDropdown" class="badge" style="display: none;">0</span>
                    </a>
                    @if($userRole === 'Admin')
                        <a href="{{ route('admin.archives') }}" class="dropdown-item">
                            <i class='bx bx-archive'></i>
                            <span>Archives</span>
                        </a>
                        <a href="{{ route('activity-logs') }}" class="dropdown-item">
                            <i class='bx bx-history'></i>
                            <span>Activity Logs</span>
                        </a>
                    @endif
                    <a href="{{ route($profileRoute === 'admin.profile' ? 'admin.profile' : ($profileRoute === 'front-desk.profile' ? 'front-desk.profile' : 'enforcer.profile')) }}" class="dropdown-item">
                        <i class='bx bx-cog'></i>
                        <span>Settings</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" id="logoutLinkDropdown" class="dropdown-item logout-item">
                        <i class='bx bx-log-out'></i>
                        <span>Logout</span>
                    </a>
                </div>

                <!-- Notification Dropdown (separate, triggered by notifications link) -->
                <div id="notificationDropdown" class="notification-dropdown" style="position: absolute; top: 50px; right: 0; background: white; border: 1px solid #ddd; border-radius: 8px; width: 350px; max-height: 500px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: none; z-index: 1001;">
                    <div style="padding: 15px; background: #f9f9f9; border-bottom: 1px solid #eee; font-weight: 600; color: #333; display: flex; justify-content: space-between; align-items: center;">
                        <span>Notifications</span>
                        <button onclick="document.getElementById('notificationDropdown').style.display='none'" style="background: none; border: none; color: #666; cursor: pointer; font-size: 18px;">&times;</button>
                        </div>
                        <div id="notificationList" style="max-height: 400px; overflow-y: auto;">
                            <div style="padding: 20px; text-align: center; color: #999;">
                                No new notifications
                            </div>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Child container -->
        <div class="child-container">
            @yield('content')
        </div>
    </div>

    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>

<style>
    /* Sidebar Account Section */
    .sidebar-account-section {
        padding: 18px 15px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border-bottom: 3px solid #004085;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
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
        flex-shrink: 0;
    }

    .account-avatar:hover {
        transform: scale(1.1);
    }

    .account-info {
        color: white;
        flex: 1;
        min-width: 0;
    }

    .account-name {
        font-weight: 700;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }

    .account-role {
        font-size: 11px;
        opacity: 0.9;
        font-weight: 500;
        text-transform: capitalize;
        line-height: 1.2;
    }

    .nav-link.logout-link {
        color: #dc3545;
        background: #fff3f3;
        border: 2px solid #ffcccc;
    }

    .nav-link.logout-link:hover {
        background: #ffcccc;
        color: #c82333;
    }

    /* Sidebar Logo and Heading Styles */
    .sidebar .logo_content {
        padding: 15px 0;
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 15px;
        text-align: center;
    }

    .logo_title {
        margin-bottom: 12px;
    }

    .logo_name {
        font-size: 24px;
        font-weight: 700;
        color: #2b35af;
        line-height: 1.3;
        margin-bottom: 2px;
    }

    .logo_subtitle {
        font-size: 11px;
        color: #666;
        font-weight: 500;
    }

    .logo_image_container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 5px 0;
    }

    .sidebar-logo {
        width: 600px;
        height: auto;
        object-fit: contain;
        max-width: 100%;
    }


    /* Topbar Profile Styles */
    .profile-dropdown-container {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 25px;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .profile-dropdown-container:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .profile-info-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        max-width: 100%;
        overflow: hidden;
    }

    .profile-avatar {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        flex-shrink: 0;
    }

    .profile-text-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 0;
        overflow: hidden;
    }

    .profile-name {
        font-size: 14px;
        font-weight: 600;
        color: #ffffff;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .profile-role {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    #dropdownChevron {
        font-size: 18px;
        color: rgba(255, 255, 255, 0.9);
        transition: transform 0.3s;
    }

    /* Profile Dropdown Styles */
    .profile-dropdown {
        position: absolute;
        top: 60px;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 12px;
        width: 280px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        z-index: 1000;
        overflow: hidden;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
    }

    .dropdown-divider {
        height: 1px;
        background: #eee;
        margin: 5px 0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s;
        position: relative;
    }

    .dropdown-item:hover {
        background: #f8f9fa;
    }

    .dropdown-item i {
        font-size: 20px;
        color: #666;
        min-width: 24px;
    }

    .dropdown-item span {
        flex: 1;
        font-size: 14px;
    }

    .dropdown-item .badge {
        background: #dc3545;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
    }

    .logout-item {
        color: #dc3545;
    }

    .logout-item:hover {
        background: #fee;
    }

    .logout-item i {
        color: #dc3545;
    }

    #dropdownChevron.active {
        transform: rotate(180deg);
    }

    .profile-dropdown-container:hover {
        opacity: 0.9;
    }

    /* Responsive Profile Styles */
    @media (max-width: 1024px) {
        .profile-dropdown-container {
            padding: 8px 10px;
            gap: 8px;
        }

        .profile-avatar {
            width: 36px;
            height: 36px;
        }

        .profile-name {
            font-size: 13px;
        }

        .profile-role {
            font-size: 10px;
        }

        #dropdownChevron {
            font-size: 16px;
        }

        .profile-dropdown {
            width: 260px;
        }
    }

    @media (max-width: 768px) {
        .profile-dropdown-container {
            padding: 6px 8px;
            gap: 6px;
        }

        .profile-avatar {
            width: 34px;
            height: 34px;
        }

        .profile-info-wrapper {
            gap: 6px;
        }

        .profile-name {
            font-size: 12px;
        }

        .profile-role {
            font-size: 9px;
            display: none;
        }

        #dropdownChevron {
            font-size: 14px;
        }

        .profile-dropdown {
            width: 240px;
            right: -10px;
        }

        .dropdown-header {
            padding: 12px;
        }

        .dropdown-item {
            padding: 10px 12px;
            font-size: 13px;
        }

        .dropdown-item i {
            font-size: 18px;
        }
    }

    @media (max-width: 480px) {
        .profile-dropdown-container {
            padding: 5px 6px;
            gap: 4px;
        }

        .profile-avatar {
            width: 32px;
            height: 32px;
        }

        .profile-info-wrapper {
            gap: 4px;
        }

        .profile-name {
            font-size: 11px;
        }

        .profile-role {
            display: none;
        }

        #dropdownChevron {
            font-size: 12px;
        }

        .profile-dropdown {
            width: 220px;
            right: -5px;
        }

        .dropdown-header {
            padding: 10px;
        }

        .dropdown-header img {
            width: 40px !important;
            height: 40px !important;
        }

        .dropdown-header div {
            min-width: 0;
        }

        .dropdown-header div div:first-child {
            font-size: 12px;
        }

        .dropdown-header div div:last-child {
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-item {
            padding: 8px 10px;
            font-size: 12px;
        }

        .dropdown-item i {
            font-size: 16px;
        }

        .dropdown-item span {
            font-size: 12px;
        }
    }

    @media (max-width: 360px) {
        .profile-dropdown-container {
            padding: 4px 4px;
            gap: 2px;
        }

        .profile-avatar {
            width: 30px;
            height: 30px;
        }

        .profile-name {
            font-size: 10px;
        }

        #dropdownChevron {
            font-size: 10px;
        }

        .profile-dropdown {
            width: 200px;
            right: 0;
            font-size: 11px;
        }

        .dropdown-header {
            padding: 8px;
        }

        .dropdown-header img {
            width: 35px !important;
            height: 35px !important;
        }

        .dropdown-item {
            padding: 6px 8px;
            font-size: 11px;
        }

        .dropdown-item i {
            font-size: 14px;
            min-width: 18px;
        }

        .dropdown-item span {
            font-size: 11px;
        }

        .profile-text-wrapper {
            display: none;
        }

        .profile-info-wrapper {
            gap: 0;
        }
    }

    /* Notification Badge for Sidebar */
    .badge-sidebar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 2px 6px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 12px;
        background: #dc3545;
        color: white;
        margin-left: auto;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nav-link .badge-sidebar {
        margin-left: auto;
        flex-shrink: 0;
    }
</style>

<script src="{{ asset('js/overlay.js') }}"></script>
<script src="{{ asset('js/payment.js') }}"></script>
<script src="{{ asset('js/pending-account.js') }}"></script>
@if($isEnforcerRole)
    <script src="{{ asset('js/gps-tracker.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof EnforcerGPSTracker === 'undefined') {
                return;
            }

            // Only initialize GPS tracking on specific pages (enforcer dashboard, gps-location)
            const currentUrl = window.location.pathname;
            const shouldEnableTracking = 
                currentUrl.includes('/enforcer/dashboard') || 
                currentUrl.includes('/enforcer/gps') ||
                currentUrl.includes('/gps-location') ||
                currentUrl.includes('/tracking');

            if (!shouldEnableTracking) {
                console.log('GPS tracking disabled on this page:', currentUrl);
                return;
            }

            if (!window.gpsTracker) {
                window.gpsTracker = new EnforcerGPSTracker({
                    updateInterval: 30000,
                    enableHighAccuracy: true,
                    timeout: 10000
                });
            }

            // Always set status to online when session is active
            window.gpsTracker.setStatus('online');

            if ({{ $currentUser->location_tracking_enabled ? 'true' : 'false' }}) {
                window.gpsTracker.startTracking();
            }
        });
    </script>
@endif
<script>
    // Sidebar Toggle for Mobile
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    let sidebarOverlay = document.querySelector('.sidebar-overlay');
    
    // Create overlay if it doesn't exist
    if (!sidebarOverlay) {
        sidebarOverlay = document.createElement('div');
        sidebarOverlay.className = 'sidebar-overlay';
        document.body.appendChild(sidebarOverlay);
    }
    
    // Toggle sidebar on button click
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });
    }
    
    // Close sidebar when clicking overlay
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });
    
    // Close sidebar when clicking a nav link
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        }
    });

    // Profile Dropdown Toggle
    const profileDropdownToggle = document.getElementById('profileDropdownToggle');
    const profileDropdown = document.getElementById('profileDropdown');
    const dropdownChevron = document.getElementById('dropdownChevron');
    const notificationsDropdownLink = document.getElementById('notificationsDropdownLink');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationList = document.getElementById('notificationList');
    const notificationBadgeDropdown = document.getElementById('notificationBadgeDropdown');

    // Toggle profile dropdown
    if (profileDropdownToggle) {
        profileDropdownToggle.addEventListener('click', function(e) {
        e.stopPropagation();
            const isOpen = profileDropdown.style.display === 'block';
            profileDropdown.style.display = isOpen ? 'none' : 'block';
            dropdownChevron.classList.toggle('active', !isOpen);
    });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (profileDropdownToggle && !profileDropdownToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.style.display = 'none';
            dropdownChevron.classList.remove('active');
        }
        if (notificationDropdown && !notificationsDropdownLink.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.style.display = 'none';
        }
    });

    // Toggle notification dropdown from menu item
    if (notificationsDropdownLink) {
        notificationsDropdownLink.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            // Close profile dropdown
            profileDropdown.style.display = 'none';
            dropdownChevron.classList.remove('active');
            // Toggle notification dropdown
            const isOpen = notificationDropdown.style.display === 'block';
            notificationDropdown.style.display = isOpen ? 'none' : 'block';
        });
    }

    // Fetch notifications every 10 seconds
    function fetchNotifications() {
        fetch('/api/notifications', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            const notifications = data.notifications || [];
            
            if (notifications.length > 0) {
                // Update badge in dropdown menu
                if (notificationBadgeDropdown) {
                    notificationBadgeDropdown.textContent = notifications.length;
                    notificationBadgeDropdown.style.display = 'inline-block';
                }
                
                // Update sidebar badge for Front Desk
                const sidebarBadge = document.getElementById('notificationBadgeSidebar');
                if (sidebarBadge) {
                    sidebarBadge.textContent = notifications.length;
                    sidebarBadge.style.display = 'inline-flex';
                }
                
                notificationList.innerHTML = notifications.map(notif => `
                    <div style="padding: 12px 15px; border-bottom: 1px solid #f0f0f0; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='white'">
                        <div style="font-weight: 600; color: #333; font-size: 13px; margin-bottom: 4px;">
                            ${notif.title}
                        </div>
                        <div style="color: #666; font-size: 12px; margin-bottom: 4px;">
                            ${notif.message}
                        </div>
                        <div style="color: #999; font-size: 11px;">
                            ${notif.time}
                        </div>
                    </div>
                `).join('');
            } else {
                if (notificationBadgeDropdown) {
                    notificationBadgeDropdown.style.display = 'none';
                }
                const sidebarBadge = document.getElementById('notificationBadgeSidebar');
                if (sidebarBadge) {
                    sidebarBadge.style.display = 'none';
                }
                notificationList.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">No new notifications</div>';
            }
        })
        .catch(error => console.log('Notification fetch error:', error));
    }

    // Initial fetch and periodic updates
    fetchNotifications();
    setInterval(fetchNotifications, 10000);

    // Sidebar account section functionality
    function toggleAccountSection(e, section) {
        e.preventDefault();
        const contentArea = document.querySelector('.child-container');
        if (!contentArea) return;
        
        // Trigger section toggle in profile page
        const sectionEvent = new CustomEvent('toggleSection', { detail: { section: section } });
        document.dispatchEvent(sectionEvent);
    }

    // Logout functionality
    function logout(e) {
        e.preventDefault();
        const logoutForm = document.getElementById('logoutForm');
        if (confirm('Are you sure you want to logout?')) {
            logoutForm.submit();
        }
    }

    // Account avatar click redirect to profile
    const accountAvatar = document.querySelector('.account-avatar');
    if (accountAvatar) {
        accountAvatar.addEventListener('click', function() {
            window.location.href = '{{ route("admin.profile") }}';
        });
    }

    // Attach logout handler for both sidebar and dropdown
    (function () {
        const logoutLink = document.getElementById('logoutLink');
        const logoutLinkDropdown = document.getElementById('logoutLinkDropdown');
        const logoutForm = document.getElementById('logoutForm');
        
        if (logoutLink && logoutForm) {
            logoutLink.addEventListener('click', function (e) {
                e.preventDefault();
                if (confirm('Are you sure you want to log out?')) {
                    logoutForm.submit();
                }
            });
        }
        
        if (logoutLinkDropdown && logoutForm) {
            logoutLinkDropdown.addEventListener('click', function (e) {
                e.preventDefault();
                if (confirm('Are you sure you want to log out?')) {
                logoutForm.submit();
                }
            });
        }
    })();
</script>

@stack('scripts')

{{-- Location Tracking for Enforcers --}}
@if(auth()->user() && strtolower(auth()->user()->role->name ?? '') === 'enforcer')
    <script src="{{ asset('js/location-tracker.js') }}"></script>
@endif

</body>
</html>
