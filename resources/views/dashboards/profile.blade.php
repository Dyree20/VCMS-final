@extends('dashboards.enforcer')

@section('content')

<div class="enforcer-profile-wrapper">
    <!-- Profile Header -->
    <section class="profile-header-card">
        <div class="profile-pic-section">
            <img id="profileImage" src="{{ $user->details && $user->details->photo ? asset('storage/' . $user->details->photo) : asset('images/default-avatar.png') }}" alt="Profile">
            <button class="change-photo-btn" id="changePhotoBtn">
                <i class="fa-solid fa-camera"></i>
            </button>
            <input type="file" id="photoInput" accept="image/*" style="display: none;">
        </div>
        <div class="profile-info-section">
            <h1>{{ $user->f_name }} {{ $user->l_name }}</h1>
            <p class="profile-role">{{ $user->role->name ?? 'User' }}</p>
            <p class="profile-email">{{ $user->email }}</p>
        </div>
    </section>

    <!-- Navigation Tabs -->
    <div class="profile-tabs">
        <button class="profile-tab active" data-tab="personal" type="button">
            <i class="fa-solid fa-user"></i>
            <span>Personal</span>
        </button>
        <button class="profile-tab" data-tab="gps" type="button">
            <i class="fa-solid fa-location-dot"></i>
            <span>GPS</span>
        </button>
        <button class="profile-tab" data-tab="settings" type="button">
            <i class="fa-solid fa-cog"></i>
            <span>Settings</span>
        </button>
    </div>

    <!-- Personal Information Tab -->
    <section class="profile-tab-content active" id="personal-tab">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-col">
                    <label>First Name</label>
                    <span>{{ $user->f_name }}</span>
                </div>
                <div class="info-col">
                    <label>Last Name</label>
                    <span>{{ $user->l_name }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-col">
                    <label>Email</label>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="info-col">
                    <label>Username</label>
                    <span>{{ $user->username }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-col">
                    <label>Enforcer ID</label>
                    <span>{{ $user->enforcer_id ?? 'N/A' }}</span>
                </div>
                <div class="info-col">
                    <label>Role</label>
                    <span>{{ $user->role->name ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-row full">
                <div class="info-col">
                    <label>Address</label>
                    <span>{{ $user->details && $user->details->address ? $user->details->address : 'Not specified' }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-col">
                    <label>Gender</label>
                    <span>{{ $user->details && $user->details->gender ? ucfirst($user->details->gender) : 'Not specified' }}</span>
                </div>
                <div class="info-col">
                    <label>Birth Date</label>
                    <span>{{ $user->details && $user->details->birthdate ? $user->details->birthdate->format('F d, Y') : 'Not specified' }}</span>
                </div>
            </div>
        </div>
        <div class="profile-actions">
            <a href="{{ route('enforcer.profile.edit') }}" class="action-btn primary">
                <i class="fa-solid fa-edit"></i> Edit Profile
            </a>
        </div>
    </section>

    <!-- GPS Tracking Tab -->
    <section class="profile-tab-content" id="gps-tab" style="display: none;">
        <div class="gps-card">
            <h3><i class="fa-solid fa-location-dot"></i> Location Tracking</h3>
            
            <div class="gps-toggle-container">
                <div class="gps-toggle-info">
                    <p class="gps-label">Enable GPS Tracking</p>
                    <p class="gps-description">Admins can see your real-time location while on duty. Updates every 30 seconds.</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="locationToggle" {{ auth()->user()->location_tracking_enabled ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div id="locationStatus" class="status-message success" style="display: {{ auth()->user()->location_tracking_enabled ? 'block' : 'none' }};">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="status-dot"></span>
                    <div>
                        <strong>Location Tracking Active</strong>
                        <p style="margin: 0; font-size: 12px;">Last update: <span id="lastUpdate">just now</span></p>
                    </div>
                </div>
            </div>

            <div id="locationError" class="status-message error" style="display: none;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div>
                        <strong>Location Error</strong>
                        <p style="margin: 0; font-size: 12px;" id="locationErrorMsg"></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Settings Tab -->
    <section class="profile-tab-content" id="settings-tab" style="display: none;">
        <div class="settings-list">
            <a href="{{ route('account.settings') }}" class="settings-item">
                <span><i class="fa-solid fa-lock"></i> Account Settings</span>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            <a href="{{ route('transactions.history') }}" class="settings-item">
                <span><i class="fa-solid fa-receipt"></i> Transactions History</span>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            <a href="{{ route('notification.settings') }}" class="settings-item">
                <span><i class="fa-solid fa-bell"></i> Notifications</span>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            <a href="{{ route('contact.us') }}" class="settings-item">
                <span><i class="fa-solid fa-envelope"></i> Contact Us</span>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
            <a href="{{ route('help.faqs') }}" class="settings-item">
                <span><i class="fa-solid fa-circle-question"></i> Help & FAQs</span>
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
        <div class="profile-actions">
            <button type="button" class="action-btn danger logout logout-btn">
                <i class="fa-solid fa-sign-out-alt"></i> Log Out
            </button>
        </div>
    </section>
</div>

<style>
    /* ===== CSS Reset for New Profile Design ===== */
    .enforcer-profile-wrapper {
        all: revert;
    }

    .enforcer-profile-wrapper * {
        all: revert;
    }

    /* ===== Main Wrapper ===== */
    .enforcer-profile-wrapper {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        font-family: "Poppins", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    /* ===== Profile Header Card ===== */
    .profile-header-card {
        background: linear-gradient(135deg, #2b58ff 0%, #1a3fa3 100%);
        color: white;
        padding: 24px;
        text-align: center;
        border-radius: 0 0 12px 12px;
        margin: 0;
        box-shadow: none;
    }

    .profile-pic-section {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 16px;
    }

    .profile-pic-section img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: none;
        margin: 0;
        padding: 0;
    }

    .change-photo-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: white;
        color: #2b58ff;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        padding: 0;
        margin: 0;
    }

    .change-photo-btn:active {
        transform: scale(0.95);
    }

    .profile-info-section h1 {
        margin: 0 0 8px 0;
        font-size: 22px;
        font-weight: 700;
        padding: 0;
        color: white;
    }

    .profile-role {
        margin: 0 0 4px 0;
        font-size: 14px;
        opacity: 0.9;
        padding: 0;
    }

    .profile-email {
        margin: 0;
        font-size: 12px;
        opacity: 0.8;
        padding: 0;
    }

    /* ===== Tabs Navigation ===== */
    .profile-tabs {
        display: flex;
        gap: 0;
        background: white;
        border-bottom: 2px solid #e8eaed;
        padding: 0;
        overflow-x: auto;
        margin: 0;
    }

    .profile-tab {
        flex: 1;
        min-width: 100px;
        padding: 14px 12px;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        color: #999;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
        margin: 0;
    }

    .profile-tab i {
        font-size: 16px;
        margin: 0;
    }

    .profile-tab:active {
        color: #2b58ff;
    }

    .profile-tab.active {
        color: #2b58ff;
        border-bottom-color: #2b58ff;
    }

    /* ===== Tab Content ===== */
    .profile-tab-content {
        padding: 16px;
        animation: fadeIn 0.2s ease;
        margin: 0;
    }

    .profile-tab-content.active {
        display: block !important;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* ===== Info Grid ===== */
    .info-grid {
        margin-bottom: 16px;
        margin: 0;
        padding: 0;
    }

    .info-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row.full {
        grid-template-columns: 1fr;
    }

    .info-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .info-col {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin: 0;
        padding: 0;
    }

    .info-col label {
        font-size: 11px;
        font-weight: 600;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        padding: 0;
    }

    .info-col span {
        font-size: 14px;
        color: #333;
        font-weight: 500;
        margin: 0;
        padding: 0;
    }

    /* ===== GPS Card ===== */
    .gps-card {
        background: white;
        margin: 0;
        padding: 0;
    }

    .gps-card h3 {
        margin: 0 0 16px 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0;
    }

    .gps-card i {
        color: #2b58ff;
    }

    .gps-toggle-container {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        border: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .gps-toggle-info {
        flex: 1;
        margin: 0;
        padding: 0;
    }

    .gps-label {
        margin: 0 0 4px 0;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        padding: 0;
    }

    .gps-description {
        margin: 0;
        font-size: 12px;
        color: #666;
        line-height: 1.4;
        padding: 0;
    }

    /* ===== Toggle Switch ===== */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.3s;
        border-radius: 26px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background-color: #28a745;
    }

    input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }

    /* ===== Status Message ===== */
    .status-message {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 12px;
        font-size: 13px;
        margin: 0 0 12px 0;
    }

    .status-message.success {
        background: #e3f2fd;
        border-left: 4px solid #2196f3;
        color: #1565c0;
    }

    .status-message.error {
        background: #ffebee;
        border-left: 4px solid #dc3545;
        color: #c41c3b;
    }

    .status-message strong {
        display: block;
        margin-bottom: 4px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #28a745;
        animation: pulse 2s infinite;
        display: inline-block;
        flex-shrink: 0;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 6px rgba(40, 167, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }

    /* ===== Settings List ===== */
    .settings-list {
        display: flex;
        flex-direction: column;
        gap: 0;
        margin-bottom: 16px;
        margin: 0;
        padding: 0;
    }

    .settings-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 12px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        text-decoration: none;
        color: #333;
        transition: background 0.2s ease;
        margin: 0;
    }

    .settings-item:first-child {
        border-radius: 8px 8px 0 0;
    }

    .settings-item:last-child {
        border-radius: 0 0 8px 8px;
        border-bottom: none;
    }

    .settings-item:active {
        background: #e8eaed;
    }

    .settings-item span {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
        margin: 0;
    }

    .settings-item i {
        font-size: 14px;
        color: #999;
    }

    .settings-item:first-child i,
    .settings-item:last-child i:last-child {
        margin-left: auto;
    }

    /* ===== Action Buttons ===== */
    .profile-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 16px;
        margin: 16px 0 0 0;
        padding: 0;
    }

    .action-btn {
        padding: 12px 16px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
        margin: 0;
    }

    .action-btn.primary {
        background: #2b58ff;
        color: white;
    }

    .action-btn.primary:active {
        background: #1a3fa3;
        transform: scale(0.98);
    }

    .action-btn.danger {
        background: #ffebee;
        color: #dc3545;
    }

    .action-btn.danger:active {
        background: #ffcdd2;
    }

    /* ===== Responsive ===== */
    @media (max-width: 640px) {
        .enforcer-profile-wrapper {
            max-width: 100%;
        }

        .profile-header-card {
            padding: 16px;
            border-radius: 0;
        }

        .profile-pic-section {
            width: 80px;
            height: 80px;
            margin-bottom: 12px;
        }

        .profile-info-section h1 {
            font-size: 18px;
        }

        .profile-tabs {
            gap: 0;
            padding: 0;
        }

        .profile-tab {
            font-size: 11px;
            padding: 12px 8px;
        }

        .profile-tab-content {
            padding: 12px;
        }

        .info-row {
            gap: 12px;
            margin-bottom: 8px;
            padding-bottom: 8px;
        }

        .info-col label {
            font-size: 10px;
        }

        .info-col span {
            font-size: 13px;
        }

        .gps-toggle-container {
            flex-direction: column;
            align-items: flex-start;
        }

        .gps-card h3 {
            font-size: 15px;
        }

        .action-btn {
            padding: 11px 14px;
            font-size: 13px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // ========== Tab Switching ==========
    const tabButtons = document.querySelectorAll('.profile-tab');
    const tabContents = document.querySelectorAll('.profile-tab-content');

    console.log('Tab buttons found:', tabButtons.length);
    console.log('Tab contents found:', tabContents.length);

    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const tabName = this.getAttribute('data-tab');
            console.log('Clicked tab:', tabName);
            
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Hide all tabs
            tabContents.forEach(content => {
                content.style.display = 'none';
                content.classList.remove('active');
            });
            
            // Show selected tab
            const selectedTab = document.getElementById(tabName + '-tab');
            console.log('Selected tab element:', selectedTab);
            
            if (selectedTab) {
                selectedTab.style.display = 'block';
                selectedTab.classList.add('active');
            }
        });
    });

    // ========== Photo Upload Handler ==========
    const changePhotoBtn = document.getElementById('changePhotoBtn');
    const photoInput = document.getElementById('photoInput');
    const profileImage = document.getElementById('profileImage');

    if (changePhotoBtn) {
        changePhotoBtn.addEventListener('click', function() {
            photoInput.click();
        });
    }

    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return;
        }

        if (file.size > 2048 * 1024) {
            alert('Image size must be less than 2MB');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            profileImage.src = e.target.result;
        };
        reader.readAsDataURL(file);

        const formData = new FormData();
        formData.append('photo', file);
        formData.append('_token', csrfToken);

        if (changePhotoBtn) {
            changePhotoBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            changePhotoBtn.disabled = true;
        }

        fetch('{{ route("profile.update-photo") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.photo_url) {
                profileImage.src = data.photo_url;
                if (typeof updateHeaderProfilePics === 'function') {
                    updateHeaderProfilePics(data.photo_url);
                }
                showNotification('Profile photo updated successfully!', 'success');
            } else {
                alert(data.message || 'Failed to update photo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while uploading the photo');
        })
        .finally(() => {
            if (changePhotoBtn) {
                changePhotoBtn.innerHTML = '<i class="fa-solid fa-camera"></i>';
                changePhotoBtn.disabled = false;
            }
            photoInput.value = '';
        });
    });

    // ========== Location Tracking Handler ==========
    const locationToggle = document.getElementById('locationToggle');
    const locationStatus = document.getElementById('locationStatus');
    const locationError = document.getElementById('locationError');
    const locationErrorMsg = document.getElementById('locationErrorMsg');
    const lastUpdate = document.getElementById('lastUpdate');
    let locationTrackingInterval = null;
    let permissionRequested = false; // Track if permission has been requested
    let permissionDenied = false; // Track if permission was denied

    if (locationToggle) {
        locationToggle.addEventListener('change', function() {
            const enabled = this.checked;
            
            if (enabled) {
                // Only request permission when user explicitly enables tracking
                requestLocationPermission(() => {
                    submitToggleUpdate(enabled);
                }, () => {
                    // Permission denied or error
                    locationToggle.checked = false;
                    permissionDenied = true;
                    showErrorMessage('Location permission denied. Please enable location access in your device settings.');
                });
            } else {
                submitToggleUpdate(enabled);
            }
        });

        if (locationToggle.checked && locationStatus) {
            locationStatus.style.display = 'block';
            // Don't automatically start tracking on page load, only show it's enabled
            // startLocationTracking will be called only after permission is granted
        }
    }

    function submitToggleUpdate(enabled) {
        fetch('{{ route("tracking.update-location") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                location_tracking_enabled: enabled
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (locationStatus) {
                    locationStatus.style.display = enabled ? 'block' : 'none';
                }
                if (locationError) {
                    locationError.style.display = 'none';
                }
                
                if (enabled) {
                    startLocationTracking();
                    showNotification('Location tracking enabled!', 'success');
                } else {
                    stopLocationTracking();
                    permissionDenied = false; // Reset permission state when disabled
                    showNotification('Location tracking disabled', 'info');
                }
            } else {
                locationToggle.checked = !enabled;
                showErrorMessage(data.message || 'Failed to update location preference');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            locationToggle.checked = !enabled;
            showErrorMessage('An error occurred');
        });
    }

    function requestLocationPermission(onSuccess, onError) {
        if (!navigator.geolocation) {
            onError('Geolocation not supported');
            return;
        }

        if (permissionDenied) {
            onError('Permission previously denied');
            return;
        }

        // Only request permission once per session
        if (permissionRequested && !permissionDenied) {
            onSuccess();
            return;
        }

        navigator.geolocation.getCurrentPosition(
            position => {
                permissionRequested = true;
                permissionDenied = false;
                onSuccess();
            },
            error => {
                permissionRequested = true;
                // Only mark as denied if it's actually a permission error (error code 1)
                // Error code 2 (POSITION_UNAVAILABLE) or 3 (TIMEOUT) shouldn't block future attempts
                if (error.code === 1) {
                    // PermissionDenied
                    permissionDenied = true;
                    console.warn('Location permission denied:', error.message);
                    onError('Location permission denied. Please enable location access in your browser settings.');
                } else {
                    // Other errors (timeout, position unavailable) - allow retry
                    console.warn('Location error (not permission):', error.message, 'Code:', error.code);
                    // Still proceed with tracking - these are temporary issues
                    onSuccess();
                }
            },
            { enableHighAccuracy: false, timeout: 5000, maximumAge: 0 }
        );
    }

    function getEnforcerLocation() {
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                resolve({ error: 'Geolocation not supported' });
                return;
            }

            navigator.geolocation.getCurrentPosition(
                position => {
                    resolve({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy_meters: Math.round(position.coords.accuracy)
                    });
                },
                error => {
                    resolve({ error: error.message });
                },
                { enableHighAccuracy: false, timeout: 8000, maximumAge: 0 }
            );
        });
    }

    async function submitLocation() {
        const location = await getEnforcerLocation();
        
        if (location.error) {
            console.warn('Location error:', location.error);
            return;
        }

        const address = await reverseGeocode(location.latitude, location.longitude);

        fetch('{{ route("tracking.update-location") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                latitude: location.latitude,
                longitude: location.longitude,
                accuracy_meters: location.accuracy_meters,
                address: address || 'Unknown location',
                status: 'online'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && lastUpdate) {
                const now = new Date();
                lastUpdate.textContent = now.toLocaleTimeString();
            }
        })
        .catch(error => console.error('Error submitting location:', error));
    }

    async function reverseGeocode(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            return data.address?.city || data.address?.town || data.address?.village || 'Location';
        } catch (error) {
            console.error('Geocoding error:', error);
            return null;
        }
    }

    function startLocationTracking() {
        if (locationTrackingInterval) return;
        submitLocation();
        locationTrackingInterval = setInterval(submitLocation, 30000);
    }

    function stopLocationTracking() {
        if (locationTrackingInterval) {
            clearInterval(locationTrackingInterval);
            locationTrackingInterval = null;
        }
    }

    function showErrorMessage(message) {
        if (locationError && locationErrorMsg) {
            locationErrorMsg.textContent = message;
            locationError.style.display = 'block';
            setTimeout(() => {
                locationError.style.display = 'none';
            }, 5000);
        }
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: ${bgColor};
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            font-weight: 500;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
});
</script>

@endsection
