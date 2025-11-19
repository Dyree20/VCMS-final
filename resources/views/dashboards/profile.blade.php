@extends('dashboards.enforcer')

@section('content')
<section class="profile-section">
    <div class="profile-pic-container">
        <img id="profileImage" src="{{ $user->details && $user->details->photo ? asset('storage/' . $user->details->photo) : asset('images/default-avatar.png') }}" alt="Profile">
        <button class="change-photo-btn" id="changePhotoBtn">+</button>
        <input type="file" id="photoInput" accept="image/*" style="display: none;">
    </div>
    <h2>{{ $user->f_name }} {{ $user->l_name }}</h2>
    <p>{{ $user->email }}</p>
</section>

<section class="info-box">
    <div class="info-item"><span>First Name</span><span>{{ $user->f_name }}</span></div>
    <div class="info-item"><span>Last Name</span><span>{{ $user->l_name }}</span></div>
    <div class="info-item"><span>Enforcer ID</span><span>{{ $user->enforcer_id ?? 'N/A' }}</span></div>
    <div class="info-item"><span>Username</span><span>{{ $user->username }}</span></div>
    <div class="info-item"><span>Email</span><span>{{ $user->email }}</span></div>
    <div class="info-item"><span>Role</span><span>{{ $user->role->name ?? 'N/A' }}</span></div>
    <div class="info-item"><span>Address</span><span>{{ $user->details && $user->details->address ? $user->details->address : 'Not specified' }}</span></div>
    <div class="info-item"><span>Gender</span><span>{{ $user->details && $user->details->gender ? $user->details->gender : 'Not specified' }}</span></div>
    <div class="info-item"><span>Birth Date</span><span>{{ $user->details && $user->details->birthdate ? $user->details->birthdate->format('F d, Y') : 'Not specified' }}</span></div>
</section>

<section class="options">
    <div class="option-item" onclick="window.location.href='{{ route('profile.edit') }}'">
        <div class="option-content">
            <span class="option-text">Edit Profile</span>
            <i class="fa-solid fa-chevron-right option-icon"></i>
        </div>
    </div>
    <div class="option-item" onclick="window.location.href='{{ route('account.settings') }}'">
        <div class="option-content">
            <span class="option-text">Account Settings</span>
            <i class="fa-solid fa-chevron-right option-icon"></i>
        </div>
    </div>
    <div class="option-item" onclick="window.location.href='{{ route('transactions.history') }}'">
        <div class="option-content">
            <span class="option-text">Transactions History</span>
            <i class="fa-solid fa-chevron-right option-icon"></i>
        </div>
    </div>
    <div class="option-item" onclick="window.location.href='{{ route('notification.settings') }}'">
        <div class="option-content">
            <span class="option-text">Notification Settings</span>
            <i class="fa-solid fa-chevron-right option-icon"></i>
        </div>
    </div>
    <div class="option-item" onclick="window.location.href='{{ route('contact.us') }}'">
        <div class="option-content">
            <span class="option-text">Contact Us</span>
            <i class="fa-solid fa-chevron-right option-icon"></i>
        </div>
    </div>
    <div class="option-item" onclick="window.location.href='{{ route('help.faqs') }}'">
        <div class="option-content">
            <span class="option-text">Help & FAQs</span>
            <i class="fa-solid fa-chevron-right option-icon"></i>
        </div>
    </div>
    <div class="option-item" onclick="window.location.href='{{ route('enforcer.location') }}'">
        <div class="option-content">
            <span class="option-text">📍 GPS Location Tracking</span>
            <i class="fa-solid fa-chevron-right option-icon"></i>
        </div>
    </div>
</section>

<!-- Location Security Section -->
<section class="security-section">
    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #333;">Location & Security</h3>
    
    <div class="security-item">
        <div class="security-content">
            <div>
                <span class="security-label">GPS Location Tracking</span>
                <p class="security-description">Enable GPS tracking so admins can see your real-time location while on duty</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="locationToggle" {{ auth()->user()->location_tracking_enabled ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>

    <div id="locationStatus" style="
        margin-top: 16px;
        padding: 12px 16px;
        border-radius: 8px;
        background: #e3f2fd;
        border-left: 4px solid #2196f3;
        display: {{ auth()->user()->location_tracking_enabled ? 'block' : 'none' }};
    ">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <span class="status-dot" id="locationStatusDot" style="
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: #28a745;
                animation: pulse 2s infinite;
            "></span>
            <span style="color: #1565c0; font-weight: 600;">Location Tracking Active</span>
        </div>
        <p style="color: #1565c0; font-size: 13px; margin: 0;">
            Your location is being updated every 30 seconds. Last update: <span id="lastUpdate">just now</span>
        </p>
    </div>
</section>

<button type="button" class="auth-button logout logout-btn">Log Out</button>

<style>
    .option-item {
        background: white;
        border-radius: 12px;
        margin-bottom: 12px;
        padding: 0;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }

    .option-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-color: #2b58ff;
    }

    .option-item:active {
        transform: translateX(3px);
    }

    .option-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 18px;
    }

    .option-text {
        font-size: 15px;
        font-weight: 500;
        color: #333;
        text-decoration: none;
    }

    .option-icon {
        font-size: 14px;
        color: #999;
        transition: all 0.3s ease;
    }

    .option-item:hover .option-icon {
        color: #2b58ff;
        transform: translateX(3px);
    }

    /* Security Section Styles */
    .security-section {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }

    .security-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 16px;
        border: 1px solid #e9ecef;
    }

    .security-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .security-label {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .security-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .security-label {
        display: block;
        font-size: 15px;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
    }

    .security-description {
        font-size: 13px;
        color: #666;
        margin: 0;
        line-height: 1.4;
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
        .security-content {
            flex-wrap: wrap;
            gap: 12px;
        }

        .security-label {
            font-size: 14px;
        }

        .security-description {
            font-size: 12px;
        }

        .toggle-switch {
            width: 45px;
            height: 26px;
            min-width: 45px;
        }

        .toggle-slider:before {
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(19px);
        }
    }

    @media (max-width: 480px) {
        .security-content {
            align-items: flex-start;
        }

        .security-item {
            padding: 12px;
        }

        .toggle-switch {
            margin-top: 22px;
        }

        .security-label {
            font-size: 13px;
        }

        .security-description {
            font-size: 11px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const changePhotoBtn = document.getElementById('changePhotoBtn');
    const photoInput = document.getElementById('photoInput');
    const profileImage = document.getElementById('profileImage');
    const locationToggle = document.getElementById('locationToggle');
    const locationStatus = document.getElementById('locationStatus');
    const lastUpdate = document.getElementById('lastUpdate');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ========== Photo Upload Handler ==========
    // Open file picker when button is clicked
    changePhotoBtn.addEventListener('click', function() {
        photoInput.click();
    });

    // Handle photo selection and upload
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2048 * 1024) {
            alert('Image size must be less than 2MB');
            return;
        }

        // Show preview immediately
        const reader = new FileReader();
        reader.onload = function(e) {
            profileImage.src = e.target.result;
        };
        reader.readAsDataURL(file);

        // Upload photo
        const formData = new FormData();
        formData.append('photo', file);
        formData.append('_token', csrfToken);

        // Show loading state
        changePhotoBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        changePhotoBtn.disabled = true;

        fetch('{{ route("profile.update-photo") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update image source with new URL
                if (data.photo_url) {
                    profileImage.src = data.photo_url;
                    // Update header profile picture if it exists (using global function if available)
                    if (typeof updateHeaderProfilePics === 'function') {
                        updateHeaderProfilePics(data.photo_url);
                    } else {
                        // Fallback: update directly
                        const headerProfilePic = document.getElementById('headerProfilePic');
                        if (headerProfilePic) {
                            headerProfilePic.src = data.photo_url;
                        }
                        document.querySelectorAll('#headerProfilePic').forEach(img => {
                            img.src = data.photo_url;
                        });
                    }
                }
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.style.cssText = 'position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: #28a745; color: white; padding: 12px 24px; border-radius: 8px; z-index: 10000; box-shadow: 0 4px 12px rgba(0,0,0,0.2);';
                successMsg.textContent = 'Profile photo updated successfully!';
                document.body.appendChild(successMsg);
                setTimeout(() => successMsg.remove(), 3000);
            } else {
                alert(data.message || 'Failed to update photo');
                // Revert image if upload failed
                profileImage.src = '{{ $user->details && $user->details->photo ? asset("storage/" . $user->details->photo) : asset("images/default-avatar.png") }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while uploading the photo');
            // Revert image
            profileImage.src = '{{ $user->details && $user->details->photo ? asset("storage/" . $user->details->photo) : asset("images/default-avatar.png") }}';
        })
        .finally(() => {
            changePhotoBtn.innerHTML = '+';
            changePhotoBtn.disabled = false;
            photoInput.value = ''; // Reset input
        });
    });

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
                locationToggle.checked = !enabled; // Revert toggle
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
