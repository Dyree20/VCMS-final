/**
 * Real-Time Location Tracking Module
 * 
 * This module handles GPS location tracking for enforcement officers
 * in the Vehicle Clamping Management System.
 * 
 * Usage:
 *   LocationTracker.start()   - Start tracking
 *   LocationTracker.stop()    - Stop tracking
 *   LocationTracker.getStatus() - Get current tracking status
 */

const LocationTracker = (function() {
    // Configuration
    const CONFIG = {
        updateInterval: 10000,      // Send location every 10 seconds
        geolocationTimeout: 5000,   // Geolocation timeout in ms
        enableHighAccuracy: true,   // Use GPS if available
        maximumAge: 0,              // Don't use cached positions
        apiEndpoint: '/gps/update-location',
        statusEndpoint: '/gps/set-status',
    };

    // State
    let state = {
        isTracking: false,
        trackingInterval: null,
        watchPositionId: null,
        lastLocation: null,
        lastUpdateTime: null,
        errorCount: 0,
        maxErrors: 5,
    };

    /**
     * Initialize location tracking
     */
    function init() {
        // Check if geolocation is supported
        if (!navigator.geolocation) {
            console.error('Geolocation is not supported by this browser');
            return false;
        }

        // Add tracking controls to the page if they don't exist
        addTrackingControls();
        restoreTrackingState();
        
        return true;
    }

    /**
     * Start tracking location
     */
    function start() {
        if (state.isTracking) {
            console.warn('Location tracking is already active');
            return;
        }

        console.log('Starting location tracking...');
        state.isTracking = true;
        state.errorCount = 0;

        // Set status to online
        setStatus('online');

        // Send location immediately
        sendCurrentLocation();

        // Set up periodic location updates
        state.trackingInterval = setInterval(() => {
            sendCurrentLocation();
        }, CONFIG.updateInterval);

        // Update UI
        updateTrackingUI('tracking');
        localStorage.setItem('locationTrackingEnabled', 'true');
    }

    /**
     * Stop tracking location
     */
    function stop() {
        if (!state.isTracking) {
            console.warn('Location tracking is not active');
            return;
        }

        console.log('Stopping location tracking...');
        state.isTracking = false;

        // Clear intervals
        if (state.trackingInterval) {
            clearInterval(state.trackingInterval);
            state.trackingInterval = null;
        }

        if (state.watchPositionId) {
            navigator.geolocation.clearWatch(state.watchPositionId);
            state.watchPositionId = null;
        }

        // Set status to offline
        setStatus('offline');

        // Update UI
        updateTrackingUI('stopped');
        localStorage.setItem('locationTrackingEnabled', 'false');
    }

    /**
     * Get current location and send to server
     */
    function sendCurrentLocation() {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const coords = position.coords;
                state.lastLocation = {
                    latitude: coords.latitude,
                    longitude: coords.longitude,
                    accuracy: coords.accuracy,
                    timestamp: new Date().toISOString(),
                };

                // Get address from coordinates (optional - reverse geocoding)
                getAddressFromCoordinates(coords.latitude, coords.longitude)
                    .then(address => {
                        sendLocationToServer(coords.latitude, coords.longitude, coords.accuracy, address);
                    })
                    .catch(() => {
                        // If address lookup fails, send without address
                        sendLocationToServer(coords.latitude, coords.longitude, coords.accuracy, null);
                    });

                state.errorCount = 0;
                updateTrackingUI('tracking');
            },
            (error) => {
                handleGeolocationError(error);
            },
            {
                enableHighAccuracy: CONFIG.enableHighAccuracy,
                timeout: CONFIG.geolocationTimeout,
                maximumAge: CONFIG.maximumAge,
            }
        );
    }

    /**
     * Send location to server
     */
    function sendLocationToServer(latitude, longitude, accuracy, address) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(CONFIG.apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                latitude: parseFloat(latitude.toFixed(8)),
                longitude: parseFloat(longitude.toFixed(8)),
                accuracy: Math.round(accuracy),
                address: address,
            }),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    state.lastUpdateTime = new Date();
                    console.log('Location updated:', data);
                    state.errorCount = 0;
                } else {
                    console.error('Failed to update location:', data.error);
                    state.errorCount++;
                    if (state.errorCount >= state.maxErrors) {
                        stop();
                    }
                }
            })
            .catch(error => {
                console.error('Error sending location:', error);
                state.errorCount++;
                if (state.errorCount >= state.maxErrors) {
                    console.warn('Max errors reached, stopping tracking');
                    stop();
                }
            });
    }

    /**
     * Get address from coordinates using OpenStreetMap Nominatim (free, no API key required)
     */
    function getAddressFromCoordinates(latitude, longitude) {
        return fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`,
            {
                headers: {
                    'Accept': 'application/json',
                },
            }
        )
            .then(response => response.json())
            .then(data => data.address?.road || data.address?.neighbourhood || data.address?.city || null)
            .catch(() => null);
    }

    /**
     * Set officer status (online/offline/on_break)
     */
    function setStatus(status) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(CONFIG.statusEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                status: status,
            }),
        })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log('Status updated:', data);
            })
            .catch(error => {
                console.error('Error updating status:', error);
            });
    }

    /**
     * Handle geolocation errors
     */
    function handleGeolocationError(error) {
        let errorMessage = 'Unknown error';
        
        switch (error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = 'Location permission denied. Please enable location access.';
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = 'Location information is unavailable.';
                break;
            case error.TIMEOUT:
                errorMessage = 'Location request timed out.';
                break;
        }

        console.error('Geolocation error:', errorMessage);
        state.errorCount++;

        if (state.errorCount >= state.maxErrors) {
            stop();
        }
    }

    /**
     * Get current tracking status
     */
    function getStatus() {
        return {
            isTracking: state.isTracking,
            lastLocation: state.lastLocation,
            lastUpdateTime: state.lastUpdateTime,
            errorCount: state.errorCount,
        };
    }

    /**
     * Add tracking controls to the page
     */
    function addTrackingControls() {
        // Check if controls already exist
        if (document.getElementById('locationTrackingControls')) {
            return;
        }

        // Create controls HTML
        const controlsHTML = `
            <div id="locationTrackingControls" style="
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: white;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 16px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                z-index: 9999;
                min-width: 200px;
                font-family: Arial, sans-serif;
            ">
                <div style="margin-bottom: 12px;">
                    <div style="font-weight: 600; font-size: 14px; margin-bottom: 4px;">
                        <i class="fa-solid fa-location-dot"></i> Location Tracking
                    </div>
                    <div id="trackingStatusIndicator" style="
                        display: inline-block;
                        width: 10px;
                        height: 10px;
                        border-radius: 50%;
                        background: #6c757d;
                        margin-right: 8px;
                    "></div>
                    <span id="trackingStatusText" style="font-size: 13px; color: #666;">Inactive</span>
                </div>
                
                <div id="trackingInfo" style="
                    background: #f8f9fa;
                    padding: 8px;
                    border-radius: 4px;
                    margin-bottom: 12px;
                    font-size: 12px;
                    color: #666;
                    display: none;
                ">
                    <div>Lat: <span id="trackingLat">--</span></div>
                    <div>Lon: <span id="trackingLon">--</span></div>
                    <div>Acc: <span id="trackingAccuracy">--</span>m</div>
                    <div style="margin-top: 4px; color: #999; font-size: 11px;">
                        Updated: <span id="trackingTime">--</span>
                    </div>
                </div>
                
                <div style="display: flex; gap: 8px;">
                    <button id="trackingToggleBtn" style="
                        flex: 1;
                        padding: 8px 12px;
                        background: #28a745;
                        color: white;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 12px;
                        font-weight: 600;
                        transition: all 0.3s ease;
                    ">
                        Start
                    </button>
                    <button id="trackingDetailsBtn" style="
                        padding: 8px 12px;
                        background: #f0f0f0;
                        color: #666;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 12px;
                    ">
                        <i class="fa-solid fa-chart-line"></i>
                    </button>
                </div>
            </div>
        `;

        // Add to page
        document.body.insertAdjacentHTML('beforeend', controlsHTML);

        // Add event listeners
        document.getElementById('trackingToggleBtn').addEventListener('click', toggleTracking);
        document.getElementById('trackingDetailsBtn').addEventListener('click', showTrackingDetails);
    }

    /**
     * Toggle tracking on/off
     */
    function toggleTracking() {
        if (state.isTracking) {
            stop();
        } else {
            start();
        }
    }

    /**
     * Update tracking UI
     */
    function updateTrackingUI(status) {
        const indicator = document.getElementById('trackingStatusIndicator');
        const statusText = document.getElementById('trackingStatusText');
        const toggleBtn = document.getElementById('trackingToggleBtn');
        const infoDiv = document.getElementById('trackingInfo');

        if (status === 'tracking') {
            indicator.style.background = '#28a745';
            statusText.textContent = 'Active';
            toggleBtn.textContent = 'Stop';
            toggleBtn.style.background = '#dc3545';
            infoDiv.style.display = 'block';

            // Update info
            if (state.lastLocation) {
                document.getElementById('trackingLat').textContent = state.lastLocation.latitude.toFixed(6);
                document.getElementById('trackingLon').textContent = state.lastLocation.longitude.toFixed(6);
                document.getElementById('trackingAccuracy').textContent = state.lastLocation.accuracy.toFixed(0);
            }

            if (state.lastUpdateTime) {
                document.getElementById('trackingTime').textContent = state.lastUpdateTime.toLocaleTimeString();
            }
        } else {
            indicator.style.background = '#6c757d';
            statusText.textContent = 'Inactive';
            toggleBtn.textContent = 'Start';
            toggleBtn.style.background = '#28a745';
            infoDiv.style.display = 'none';
        }
    }

    /**
     * Show tracking details in an alert
     */
    function showTrackingDetails() {
        const status = getStatus();
        if (!status.lastLocation) {
            alert('No location data available');
            return;
        }

        const details = `
Location Tracking Status

Status: ${status.isTracking ? 'ACTIVE' : 'INACTIVE'}

Latitude: ${status.lastLocation.latitude.toFixed(8)}
Longitude: ${status.lastLocation.longitude.toFixed(8)}
Accuracy: ±${status.lastLocation.accuracy.toFixed(0)} meters

Last Update: ${status.lastUpdateTime ? status.lastUpdateTime.toLocaleTimeString() : 'Never'}
Errors: ${status.errorCount}/${state.maxErrors}
        `;

        alert(details);
    }

    /**
     * Restore tracking state from localStorage
     */
    function restoreTrackingState() {
        const wasEnabled = localStorage.getItem('locationTrackingEnabled') === 'true';
        if (wasEnabled && navigator.geolocation) {
            // Auto-start tracking if it was enabled before
            start();
        }
    }

    // Public API
    return {
        init: init,
        start: start,
        stop: stop,
        getStatus: getStatus,
        sendLocation: sendCurrentLocation,
    };
})();

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        LocationTracker.init();
    });
} else {
    LocationTracker.init();
}
