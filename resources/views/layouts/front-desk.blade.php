<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Front Desk Dashboard')</title>

    <link rel="stylesheet" href="/../../styles/users.css">
    <link rel="stylesheet" href="/../../styles/payments.css">
    <link rel="stylesheet" href="/../../styles/clamping.css">
    <link rel="stylesheet" href="/../../styles/dashboard.css">
    <link rel="stylesheet" href="/../../styles/style.css">
    <link rel="stylesheet" href="/../../styles/dialog.css">
    <link rel="stylesheet" href="/../../styles/overlay.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
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
                    @switch($currentRoute)
                        @case('front-desk.dashboard')
                            Front Desk Management
                        @break
                        @case('front-desk.violations')
                            Front Desk Management
                        @break
                        @case('front-desk.payments')
                            Front Desk Management
                        @break
                        @case('front-desk.archives')
                            Front Desk Management
                        @break
                        @default
                            Front Desk Management
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav">
            <li>
                <a href="{{ route('front-desk.dashboard') }}" class="nav-link {{ $currentRoute === 'front-desk.dashboard' ? 'active' : '' }}">
                    <i class='bx bx-grid-alt'></i>
                    <span class="link_name">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('front-desk.violations') }}" class="nav-link {{ $currentRoute === 'front-desk.violations' ? 'active' : '' }}">
                    <i class='bx bx-car'></i>
                    <span class="link_name">Violations</span>
                </a>
            </li>
            <li>
                <a href="{{ route('front-desk.payments') }}" class="nav-link {{ $currentRoute === 'front-desk.payments' ? 'active' : '' }}">
                    <i class='bx bx-wallet'></i>
                    <span class="link_name">Payments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('front-desk.archives') }}" class="nav-link {{ $currentRoute === 'front-desk.archives' ? 'active' : '' }}">
                    <i class='bx bx-archive'></i>
                    <span class="link_name">Archives</span>
                </a>
            </li>

            <!-- Always visible: Settings link -->
            <li style="border-top: 1px solid #e0e0e0; margin-top: auto; padding-top: 15px;">
                <a href="{{ route('admin.profile') }}" class="nav-link">
                    <i class='fa-solid fa-cog'></i>
                    <span class="link_name">Settings</span>
                </a>
            </li>
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
            <div class="text">
                @switch($currentRoute)
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
                    @default
                        Front Desk Management
                @endswitch
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
                            <span class="profile-role">{{ $currentUser->role->name ?? 'Front Desk' }}</span>
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
                    <a href="{{ route('admin.profile') }}" class="dropdown-item">
                        <i class='fa-solid fa-user'></i>
                        <span>Profile</span>
                    </a>
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

    .sidebar-logo {
        width: 600px;
        height: auto;
        object-fit: contain;
        max-width: 100%;
    }

    /* Account Section Styles */
    .sidebar-account-section {
        padding: 18px 15px;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border-radius: 8px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        border: 2px solid #004085;
        transition: transform 0.2s;
    }

    .sidebar-account-section:hover {
        transform: translateY(-2px);
    }

    .account-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 3px solid white;
        object-fit: cover;
    }

    .account-info {
        flex: 1;
        color: white;
    }

    .account-name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .account-role {
        font-size: 11px;
        opacity: 0.9;
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
    }

    .profile-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .profile-text-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .profile-name {
        font-size: 14px;
        font-weight: 600;
        color: #ffffff;
        line-height: 1.2;
    }

    .profile-role {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 2px;
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
</style>

<script src="{{ asset('js/overlay.js') }}"></script>
<script src="{{ asset('js/payment.js') }}"></script>
<script>
    function logout(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            document.getElementById('logoutForm').submit();
        }
    }

    function toggleAccountSection(e, section) {
        e.preventDefault();
    }

    // Profile Dropdown Toggle
    const profileDropdownToggle = document.getElementById('profileDropdownToggle');
    const profileDropdown = document.getElementById('profileDropdown');
    const dropdownChevron = document.getElementById('dropdownChevron');

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
    });
</script>

@stack('scripts') 

</body>
</html>
