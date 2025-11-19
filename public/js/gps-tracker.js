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
        this.isStarting = false;
        this.watcherId = null;
        this.lastUpdate = null;
    }

    /**
     * Start tracking enforcer location
     */
    startTracking() {
        if (this.isTracking || this.isStarting) {
            console.log('GPS tracking already running or starting.');
            return true;
        }

        console.log('🚀 Starting GPS tracking...');
        
        if (!navigator.geolocation) {
            console.error('Geolocation is not supported by this browser');
            this.showNotification('GPS not supported', 'error');
            return false;
        }

        this.isStarting = true;

        // Check if location tracking is enabled
        this.checkLocationTrackingEnabled().then(enabled => {
            if (!enabled) {
                this.showNotification('Location tracking is not enabled', 'warning');
                this.isStarting = false;
                return;
            }

            if (this.isTracking) {
                this.isStarting = false;
                return;
            }

            this.isTracking = true;
            this.isStarting = false;
            
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
            this.setStatus('online');
        }).catch(() => {
            this.isStarting = false;
        });
    }

    /**
     * Stop tracking
     */
    stopTracking() {
        if (this.watcherId !== null) {
            navigator.geolocation.clearWatch(this.watcherId);
            console.log('🛑 GPS tracking stopped');
            this.isTracking = false;
            this.isStarting = false;
            this.watcherId = null;
            this.updateUI('offline');
            this.setStatus('offline');
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
