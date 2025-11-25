/**
 * Enforcer GPS Tracking Script
 * This script runs on the enforcer's phone/browser to track their location
 */

class EnforcerGPSTracker {
    constructor(options = {}) {
        this.updateInterval = options.updateInterval || 30000; // 30 seconds
        this.enableHighAccuracy = options.enableHighAccuracy !== false;
        this.timeout = options.timeout || 10000;
        this.maxAge = options.maxAge || 0;
        this.isTracking = false;
        this.watcherId = null;
        this.lastUpdate = null;
    }

    /**
     * Start tracking enforcer location
     */
    startTracking() {
        console.log('🚀 Starting GPS tracking...');
        
        if (!navigator.geolocation) {
            console.error('Geolocation is not supported by this browser');
            this.showNotification('GPS not supported', 'error');
            return false;
        }

        // Request location permission explicitly
        this.requestLocationPermission().then(permissionGranted => {
            if (!permissionGranted) {
                this.showNotification('Location permission denied. Enable in browser settings.', 'error');
                return;
            }

            // Check if location tracking is enabled in app settings
            this.checkLocationTrackingEnabled().then(enabled => {
                if (!enabled) {
                    this.showNotification('Location tracking is not enabled in app settings', 'warning');
                    return;
                }

                this.isTracking = true;
                
                // Watch position (continuous updates)
                this.watcherId = navigator.geolocation.watchPosition(
                    (position) => this.handleLocationSuccess(position),
                    (error) => this.handleLocationError(error),
                    {
                        enableHighAccuracy: this.enableHighAccuracy,
                        timeout: this.timeout,
                        maximumAge: this.maxAge
                    }
                );

                console.log('✅ GPS tracking started. Watcher ID:', this.watcherId);
                this.updateUI('online');
                this.showNotification('📍 Location tracking started - updates every 30 seconds', 'success');
            });
        });
    }

    /**
     * Request location permission from browser
     * Similar to camera/microphone permission requests
     */
    requestLocationPermission() {
        return new Promise((resolve) => {
            // Check if Permissions API is available
            if (!navigator.permissions || !navigator.permissions.query) {
                // Fallback: Try to get current position to trigger permission prompt
                console.log('📍 Permissions API not available. Using fallback method.');
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        console.log('✅ Location permission granted');
                        resolve(true);
                    },
                    (error) => {
                        if (error.code === error.PERMISSION_DENIED) {
                            console.error('❌ Location permission denied');
                            resolve(false);
                        } else {
                            // Other errors, but permission might be granted
                            console.warn('GPS error:', error);
                            resolve(true);
                        }
                    },
                    { enableHighAccuracy: true, timeout: 5000 }
                );
                return;
            }

            // Use Permissions API if available
            navigator.permissions.query({ name: 'geolocation' })
                .then((permissionStatus) => {
                    console.log('📍 Location Permission Status:', permissionStatus.state);

                    if (permissionStatus.state === 'granted') {
                        console.log('✅ Location permission already granted');
                        resolve(true);
                    } else if (permissionStatus.state === 'denied') {
                        console.error('❌ Location permission denied');
                        this.showPermissionDeniedDialog();
                        resolve(false);
                    } else {
                        // 'prompt' state - show permission dialog
                        console.log('⏳ Requesting location permission from user...');
                        this.showPermissionPromptDialog().then((userChoice) => {
                            resolve(userChoice);
                        });
                    }

                    // Listen for permission changes
                    permissionStatus.addEventListener('change', () => {
                        console.log('📍 Permission status changed:', permissionStatus.state);
                        if (permissionStatus.state === 'denied' && this.isTracking) {
                            this.stopTracking();
                            this.showNotification('Location permission revoked', 'error');
                        }
                    });
                })
                .catch((error) => {
                    console.warn('⚠️ Could not check permission status:', error);
                    // Assume we can try to access location
                    resolve(true);
                });
        });
    }

    /**
     * Show permission prompt dialog (like camera permission)
     */
    showPermissionPromptDialog() {
        return new Promise((resolve) => {
            const dialog = document.createElement('div');
            dialog.id = 'location-permission-dialog';
            dialog.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10001;
                animation: fadeIn 0.3s ease-out;
            `;

            dialog.innerHTML = `
                <div style="
                    background: white;
                    border-radius: 12px;
                    padding: 24px;
                    max-width: 400px;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                    animation: slideUp 0.3s ease-out;
                ">
                    <div style="text-align: center; margin-bottom: 16px;">
                        <div style="font-size: 48px; margin-bottom: 12px;">📍</div>
                        <h2 style="margin: 0 0 8px; font-size: 20px; color: #333;">Allow Location Access?</h2>
                        <p style="margin: 0; color: #666; font-size: 14px; line-height: 1.5;">
                            This app needs access to your device's location to accurately track your position during work hours. Location data helps supervisors monitor enforcer activities and improve operational efficiency.
                        </p>
                    </div>

                    <div style="
                        background: #f5f5f5;
                        border-left: 4px solid #2196f3;
                        padding: 12px;
                        border-radius: 4px;
                        margin-bottom: 20px;
                        font-size: 13px;
                        color: #666;
                    ">
                        <strong style="color: #2196f3;">📌 Location will be:</strong>
                        <ul style="margin: 8px 0 0; padding-left: 20px;">
                            <li>Updated every 30 seconds</li>
                            <li>Only visible to authorized admins</li>
                            <li>Used only during tracking</li>
                            <li>Encrypted and secure</li>
                        </ul>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button id="location-deny-btn" style="
                            flex: 1;
                            padding: 10px;
                            border: 2px solid #ddd;
                            background: white;
                            border-radius: 6px;
                            cursor: pointer;
                            font-weight: 600;
                            color: #333;
                            transition: all 0.3s;
                        ">
                            Deny
                        </button>
                        <button id="location-allow-btn" style="
                            flex: 1;
                            padding: 10px;
                            background: #2196f3;
                            color: white;
                            border: none;
                            border-radius: 6px;
                            cursor: pointer;
                            font-weight: 600;
                            transition: all 0.3s;
                        ">
                            Allow
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(dialog);

            const denyBtn = document.getElementById('location-deny-btn');
            const allowBtn = document.getElementById('location-allow-btn');

            denyBtn.addEventListener('click', () => {
                dialog.remove();
                resolve(false);
            });

            denyBtn.addEventListener('mouseover', () => {
                denyBtn.style.background = '#f5f5f5';
            });

            denyBtn.addEventListener('mouseout', () => {
                denyBtn.style.background = 'white';
            });

            allowBtn.addEventListener('click', () => {
                dialog.remove();
                // Actually request location
                navigator.geolocation.getCurrentPosition(
                    () => resolve(true),
                    (error) => {
                        if (error.code === error.PERMISSION_DENIED) {
                            resolve(false);
                        } else {
                            resolve(true);
                        }
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            });

            allowBtn.addEventListener('mouseover', () => {
                allowBtn.style.background = '#1976d2';
            });

            allowBtn.addEventListener('mouseout', () => {
                allowBtn.style.background = '#2196f3';
            });
        });
    }

    /**
     * Show permission denied dialog
     */
    showPermissionDeniedDialog() {
        const dialog = document.createElement('div');
        dialog.id = 'location-denied-dialog';
        dialog.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            animation: fadeIn 0.3s ease-out;
        `;

        dialog.innerHTML = `
            <div style="
                background: white;
                border-radius: 12px;
                padding: 24px;
                max-width: 400px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                animation: slideUp 0.3s ease-out;
            ">
                <div style="text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 12px;">🚫</div>
                    <h2 style="margin: 0 0 8px; font-size: 20px; color: #d32f2f;">Location Permission Denied</h2>
                    <p style="margin: 0 0 16px; color: #666; font-size: 14px; line-height: 1.5;">
                        To use GPS tracking, you need to enable location access in your browser settings.
                    </p>

                    <div style="
                        background: #fff3e0;
                        border-left: 4px solid #ff9800;
                        padding: 12px;
                        border-radius: 4px;
                        margin-bottom: 20px;
                        font-size: 13px;
                        color: #666;
                        text-align: left;
                    ">
                        <strong style="color: #ff9800;">How to enable location:</strong>
                        <ul style="margin: 8px 0 0; padding-left: 20px;">
                            <li><strong>Chrome:</strong> Settings → Privacy & Security → Site Settings → Location</li>
                            <li><strong>Firefox:</strong> Preferences → Privacy → Permissions → Location</li>
                            <li><strong>Safari:</strong> Preferences → Privacy → Location Services</li>
                        </ul>
                    </div>

                    <button id="location-close-btn" style="
                        width: 100%;
                        padding: 10px;
                        background: #d32f2f;
                        color: white;
                        border: none;
                        border-radius: 6px;
                        cursor: pointer;
                        font-weight: 600;
                        transition: all 0.3s;
                    ">
                        Okay
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(dialog);

        const closeBtn = document.getElementById('location-close-btn');
        closeBtn.addEventListener('click', () => {
            dialog.remove();
        });

        closeBtn.addEventListener('mouseover', () => {
            closeBtn.style.background = '#b71c1c';
        });

        closeBtn.addEventListener('mouseout', () => {
            closeBtn.style.background = '#d32f2f';
        });
    }

    /**
    stopTracking() {
        if (this.watcherId !== null) {
            navigator.geolocation.clearWatch(this.watcherId);
            console.log('🛑 GPS tracking stopped');
            this.isTracking = false;
            this.updateUI('offline');
        }
    }

    /**
     * Handle successful location update
     */
    handleLocationSuccess(position) {
        const coords = position.coords;
        const timestamp = new Date(position.timestamp);

        console.log(`📍 Location Updated:
            - Latitude: ${coords.latitude}
            - Longitude: ${coords.longitude}
            - Accuracy: ${coords.accuracy}m
            - Altitude: ${coords.altitude}m
            - Speed: ${coords.speed} m/s
            - Timestamp: ${timestamp}`);

        // Send location to server
        this.sendLocationToServer({
            latitude: coords.latitude,
            longitude: coords.longitude,
            accuracy: Math.round(coords.accuracy),
            speed: coords.speed,
            altitude: coords.altitude,
            timestamp: timestamp.toISOString()
        });

        // Update UI
        this.updateLocationUI(coords);
    }

    /**
     * Handle location errors
     */
    handleLocationError(error) {
        let errorMessage = '';
        
        switch (error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = 'Location permission denied. Enable in settings.';
                console.error('❌ Permission Denied - User refused location access');
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = 'Location not available. Try moving to open area.';
                console.error('❌ Position Unavailable - GPS signal not available');
                break;
            case error.TIMEOUT:
                errorMessage = 'Location request timeout. Retrying...';
                console.error('❌ Timeout - GPS took too long to respond');
                break;
            default:
                errorMessage = 'Unknown location error. Please try again.';
        }

        console.error(errorMessage);
        this.showNotification(errorMessage, 'error');
    }

    /**
     * Send location to server
     */
    sendLocationToServer(locationData) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            return;
        }

        const now = Date.now();
        
        // Throttle requests - don't send more than once per 30 seconds
        if (this.lastUpdate && (now - this.lastUpdate) < this.updateInterval) {
            return;
        }

        fetch('/gps/update-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                latitude: locationData.latitude,
                longitude: locationData.longitude,
                accuracy: locationData.accuracy,
                address: this.getAddressFromCoords(locationData)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Location sent to server:', data.message);
                this.lastUpdate = now;
            } else {
                console.error('❌ Server error:', data.error);
            }
        })
        .catch(error => {
            console.error('❌ Failed to send location:', error);
        });
    }

    /**
     * Get address from coordinates using Reverse Geocoding
     * (requires a geocoding API or you can leave it empty for now)
     */
    getAddressFromCoords(coords) {
        // This is a placeholder - you can integrate Google Maps API or other geocoding service
        return `${coords.latitude.toFixed(4)}, ${coords.longitude.toFixed(4)}`;
    }

    /**
     * Check if location tracking is enabled in settings
     */
    checkLocationTrackingEnabled() {
        return fetch('/gps/current-location', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => {
            if (response.status === 403) {
                return false;
            }
            return true;
        })
        .catch(() => false);
    }

    /**
     * Set enforcer status (online/offline/on_break)
     */
    setStatus(status) {
        if (!['online', 'offline', 'on_break'].includes(status)) {
            console.error('Invalid status:', status);
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            return;
        }

        fetch('/gps/set-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Status updated:', data.message);
                this.updateUI(status);
            } else {
                console.error('❌ Error:', data.error);
            }
        })
        .catch(error => console.error('❌ Failed to set status:', error));
    }

    /**
     * Update location UI with coordinates and accuracy
     */
    updateLocationUI(coords) {
        const locationDisplay = document.getElementById('gps-location-display');
        if (locationDisplay) {
            locationDisplay.innerHTML = `
                <div class="location-info">
                    <p><strong>📍 Latitude:</strong> ${coords.latitude.toFixed(6)}</p>
                    <p><strong>📍 Longitude:</strong> ${coords.longitude.toFixed(6)}</p>
                    <p><strong>🎯 Accuracy:</strong> ${Math.round(coords.accuracy)}m</p>
                    <p><strong>⏱️ Last Update:</strong> ${new Date().toLocaleTimeString()}</p>
                </div>
            `;
        }
    }

    /**
     * Update status UI
     */
    updateUI(status) {
        const statusBadge = document.getElementById('gps-status-badge');
        if (statusBadge) {
            statusBadge.className = `status-badge status-${status}`;
            statusBadge.textContent = status.toUpperCase();
        }
    }

    /**
     * Show notification to user
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `gps-notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 20px;
            background: ${type === 'error' ? '#f44336' : type === 'warning' ? '#ff9800' : '#4caf50'};
            color: white;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    /**
     * Get all online enforcers (for admin view)
     */
    static getOnlineEnforcers() {
        return fetch('/gps/online-enforcers', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .catch(error => {
            console.error('Failed to fetch online enforcers:', error);
            return { success: false, enforcers: [] };
        });
    }

    /**
     * Get location history for an enforcer (for admin view)
     */
    static getLocationHistory(userId, hours = 24) {
        return fetch(`/gps/location-history/${userId}?hours=${hours}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .catch(error => {
            console.error('Failed to fetch location history:', error);
            return { success: false, locations: [] };
        });
    }
}

// Initialize GPS Tracker when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on enforcer dashboard and if user is an enforcer
    const gpsContainer = document.getElementById('gps-tracker-container');
    if (gpsContainer) {
        window.gpsTracker = new EnforcerGPSTracker({
            updateInterval: 30000, // 30 seconds
            enableHighAccuracy: true,
            timeout: 10000
        });

        // Auto-start tracking if there's a start button
        const startBtn = document.getElementById('gps-start-btn');
        if (startBtn) {
            startBtn.addEventListener('click', () => {
                window.gpsTracker.startTracking();
            });
        }

        // Stop button
        const stopBtn = document.getElementById('gps-stop-btn');
        if (stopBtn) {
            stopBtn.addEventListener('click', () => {
                window.gpsTracker.stopTracking();
            });
        }

        // Status buttons
        document.querySelectorAll('[data-status]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const status = e.target.dataset.status;
                window.gpsTracker.setStatus(status);
            });
        });
    }
});

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
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
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Permission Dialog Styles */
    .permission-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease-out;
    }

    .permission-dialog {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        max-width: 500px;
        width: 90%;
        overflow: hidden;
        animation: slideUp 0.3s ease-out;
    }

    .permission-dialog-header {
        background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
        color: white;
        padding: 24px;
        text-align: center;
    }

    .permission-dialog-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    .permission-dialog-title {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }

    .permission-dialog-body {
        padding: 24px;
    }

    .permission-dialog-text {
        color: #555;
        line-height: 1.6;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .permission-benefits {
        background: #f0f7ff;
        border-left: 4px solid #2196f3;
        padding: 12px;
        margin: 12px 0;
        border-radius: 4px;
        font-size: 13px;
    }

    .permission-benefits ul {
        margin: 8px 0;
        padding-left: 20px;
    }

    .permission-benefits li {
        margin: 4px 0;
        color: #333;
    }

    .permission-security {
        background: #f5f5f5;
        border-radius: 4px;
        padding: 12px;
        margin: 12px 0;
        font-size: 12px;
        color: #666;
    }

    .permission-instructions {
        background: #fff3e0;
        border-left: 4px solid #ff9800;
        padding: 12px;
        margin: 12px 0;
        border-radius: 4px;
        font-size: 13px;
    }

    .permission-instructions-title {
        font-weight: 600;
        color: #e65100;
        margin-bottom: 8px;
    }

    .permission-instructions ol {
        margin: 8px 0;
        padding-left: 20px;
    }

    .permission-instructions li {
        margin: 4px 0;
        color: #333;
        font-size: 12px;
    }

    .permission-dialog-footer {
        background: #f9f9f9;
        padding: 16px 24px;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        border-top: 1px solid #e0e0e0;
    }

    .permission-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 14px;
    }

    .permission-btn-primary {
        background: #2196f3;
        color: white;
    }

    .permission-btn-primary:hover {
        background: #1976d2;
    }

    .permission-btn-secondary {
        background: #e0e0e0;
        color: #333;
    }

    .permission-btn-secondary:hover {
        background: #d0d0d0;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-online {
        background: #4caf50;
        color: white;
    }

    .status-offline {
        background: #f44336;
        color: white;
    }

    .status-on_break {
        background: #ff9800;
        color: white;
    }

    .gps-controls {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
    }

    .gps-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }

    .gps-btn-primary {
        background: #4caf50;
        color: white;
    }

    .gps-btn-primary:hover {
        background: #388e3c;
    }

    .gps-btn-danger {
        background: #f44336;
        color: white;
    }

    .gps-btn-danger:hover {
        background: #d32f2f;
    }

    .location-info {
        background: #f5f5f5;
        padding: 12px;
        border-radius: 4px;
        font-size: 13px;
    }

    .location-info p {
        margin: 4px 0;
    }
`;
document.head.appendChild(style);
