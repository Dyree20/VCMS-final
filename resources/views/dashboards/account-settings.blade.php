@extends('dashboards.enforcer')

@section('content')
<div class="account-settings-container">
    <a href="{{ route('enforcer.profile') }}" class="back-btn">← Back to Profile</a>
    
    <h1 class="page-title">Account Settings</h1>

    <!-- Security Section -->
    <section class="settings-section">
        <h2 class="section-title">Security</h2>

        <!-- Device Manager -->
        <div class="setting-card">
            <div class="setting-card-header">
                <div class="setting-icon">
                    <i class='bx bx-devices'></i>
                </div>
                <div class="setting-content">
                    <h3>Device Manager</h3>
                    <p>View and manage all devices logged into your account.</p>
                </div>
            </div>
            <a href="{{ route('devices.index') }}" class="setting-button">
                <span>→ Manage Devices</span>
            </a>
        </div>

        <!-- Change Password -->
        <div class="setting-card">
            <div class="setting-card-header">
                <div class="setting-icon">
                    <i class='bx bx-lock'></i>
                </div>
                <div class="setting-content">
                    <h3>Change Password</h3>
                    <p>Update your password to keep your account secure.</p>
                </div>
            </div>
            <a href="{{ route('enforcer.profile.edit') }}" class="setting-button">
                <span>Change Password</span>
            </a>
        </div>

        <!-- Two-Factor Authentication -->
        <div class="setting-card">
            <div class="setting-card-header">
                <div class="setting-icon">
                    <i class='bx bx-shield-alt-2'></i>
                </div>
                <div class="setting-content">
                    <h3>Two-Factor Authentication</h3>
                    <p>Add an extra layer of security to your account.</p>
                </div>
            </div>
            <button class="setting-button" onclick="alert('2FA coming soon')">
                <span>Enable 2FA</span>
            </button>
        </div>

        <!-- GPS Location Tracking -->
        <div class="setting-card location-card">
            <div class="setting-card-header-location">
                <div class="setting-icon">
                    <i class='bx bx-map-pin'></i>
                </div>
                <div class="setting-content">
                    <h3>GPS Location Tracking</h3>
                    <p>Enable GPS tracking so admins can see your real-time location while on duty.</p>
                </div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="locationToggle" {{ auth()->user()->location_tracking_enabled ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <!-- Location Status Display -->
        <div id="locationStatus" class="location-status-card" style="display: {{ auth()->user()->location_tracking_enabled ? 'block' : 'none' }};">
            <div class="status-header">
                <span class="status-dot" style="
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    background-color: #28a745;
                    display: inline-block;
                    animation: pulse 2s infinite;
                "></span>
                <span class="status-text">Location Tracking Active</span>
            </div>
            <p class="status-description">
                Your location is being updated every 30 seconds. Last update: <span id="lastUpdate">just now</span>
            </p>
        </div>
    </section>
</div>

<style>
    .account-settings-container {
        max-width: 700px;
        margin: 0 auto;
        padding: 20px;
    }

    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        color: #2b58ff;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        color: #1e42cc;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 30px;
    }

    .settings-section {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f0f0f0;
    }

    .setting-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        margin-bottom: 16px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .setting-card:hover {
        background: #f0f1f5;
        border-color: #dee2e6;
    }

    .setting-card-header {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
    }

    .setting-card-header-location {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
    }

    .setting-icon {
        width: 48px;
        height: 48px;
        background: #2b58ff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        flex-shrink: 0;
    }

    .setting-content h3 {
        font-size: 15px;
        font-weight: 600;
        color: #333;
        margin: 0 0 4px 0;
    }

    .setting-content p {
        font-size: 12px;
        color: #666;
        margin: 0;
        line-height: 1.4;
    }

    .setting-button {
        padding: 10px 20px;
        background: #2b58ff;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .setting-button:hover {
        background: #1e42cc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 88, 255, 0.3);
    }

    /* Location Card Specific */
    .location-card {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-wrap: nowrap !important;
        gap: 16px !important;
    }

    .location-card .setting-card-header-location {
        flex: 1;
    }

    .location-card .toggle-switch {
        flex-shrink: 0;
    }

    /* Toggle Switch Styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
        flex-shrink: 0;
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
        transition: 0.4s;
        border-radius: 28px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background-color: #28a745;
    }

    input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }

    /* Location Status Card */
    .location-status-card {
        margin-top: 16px;
        padding: 16px;
        background: #e3f2fd;
        border: 1px solid #90caf9;
        border-left: 4px solid #2196f3;
        border-radius: 8px;
    }

    .status-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .status-text {
        font-weight: 600;
        color: #1565c0;
        font-size: 14px;
    }

    .status-description {
        color: #1565c0;
        font-size: 12px;
        margin: 0;
        line-height: 1.4;
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

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .account-settings-container {
            padding: 12px;
        }

        .page-title {
            font-size: 22px;
            margin-bottom: 20px;
        }

        .settings-section {
            padding: 16px;
            margin-bottom: 20px;
        }

        .setting-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
        }

        .setting-card-header {
            width: 100%;
        }

        .setting-button {
            width: 100%;
            justify-content: center;
        }

        .setting-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }

        .setting-content h3 {
            font-size: 14px;
        }

        .setting-content p {
            font-size: 11px;
        }

        .toggle-switch {
            width: 45px;
            height: 26px;
            align-self: flex-end;
        }

        .toggle-slider:before {
            height: 20px;
            width: 20px;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(19px);
        }
    }

    @media (max-width: 480px) {
        .account-settings-container {
            padding: 8px;
        }

        .page-title {
            font-size: 18px;
            margin-bottom: 16px;
        }

        .settings-section {
            padding: 12px;
        }

        .section-title {
            font-size: 16px;
            margin-bottom: 12px;
        }

        .setting-card {
            padding: 10px;
            margin-bottom: 12px;
        }

        .setting-icon {
            width: 36px;
            height: 36px;
            font-size: 18px;
        }

        .setting-content h3 {
            font-size: 13px;
            margin-bottom: 2px;
        }

        .setting-content p {
            font-size: 10px;
        }

        .setting-button {
            padding: 8px 16px;
            font-size: 12px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const locationToggle = document.getElementById('locationToggle');
    const locationStatus = document.getElementById('locationStatus');
    const lastUpdate = document.getElementById('lastUpdate');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ========== Location Tracking Handler ==========
    locationToggle.addEventListener('change', function() {
        const enabled = this.checked;
        
        // Save preference to backend
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
                // Show/hide status box
                locationStatus.style.display = enabled ? 'block' : 'none';
                
                if (enabled) {
                    // Start location tracking
                    startLocationTracking();
                    showNotification('Location tracking enabled!', 'success');
                } else {
                    // Stop location tracking
                    stopLocationTracking();
                    showNotification('Location tracking disabled', 'info');
                }
            } else {
                locationToggle.checked = !this.checked; // Revert toggle
                showNotification(data.message || 'Failed to update location preference', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            locationToggle.checked = !enabled; // Revert toggle
            showNotification('An error occurred', 'error');
        });
    });

    // Get enforcer's current location
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
                        accuracy_meters: Math.round(position.coords.accuracy),
                        address: 'Getting address...'
                    });
                },
                error => {
                    resolve({ error: error.message });
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }

    // Submit location to server
    async function submitLocation() {
        const location = await getEnforcerLocation();
        
        if (location.error) {
            console.warn('Location error:', location.error);
            return;
        }

        // Reverse geocode to get address
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
            if (data.success) {
                const now = new Date();
                lastUpdate.textContent = now.toLocaleTimeString();
                console.log('Location updated successfully');
            }
        })
        .catch(error => console.error('Error submitting location:', error));
    }

    // Reverse geocode coordinates to address (using free API)
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

    let locationTrackingInterval = null;

    // Start continuous location tracking
    function startLocationTracking() {
        if (locationTrackingInterval) return; // Already tracking

        // Submit location immediately
        submitLocation();

        // Submit location every 30 seconds
        locationTrackingInterval = setInterval(submitLocation, 30000);
    }

    // Stop location tracking
    function stopLocationTracking() {
        if (locationTrackingInterval) {
            clearInterval(locationTrackingInterval);
            locationTrackingInterval = null;
        }
    }

    // Show notification
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

    // Auto-start tracking if already enabled
    if (locationToggle.checked) {
        startLocationTracking();
    }
});
</script>

@endsection
