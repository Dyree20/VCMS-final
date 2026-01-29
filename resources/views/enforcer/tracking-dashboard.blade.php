@extends('layouts.app')

@section('title', 'My Tracking Dashboard')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

@section('content')
<div class="enforcer-dashboard-container">
    <div class="dashboard-header">
        <h1><i class="fa-solid fa-map"></i> My Tracking Dashboard</h1>
        <p>Your location history and activity statistics</p>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-icon" style="color: #28a745;">
                <i class="fa-solid fa-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Current Status</div>
                <div class="stat-value" id="currentStatus">Offline</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-icon" style="color: #007bff;">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Location Updates</div>
                <div class="stat-value" id="locationCount">0</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-icon" style="color: #ffc107;">
                <i class="fa-solid fa-ruler"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Distance Traveled</div>
                <div class="stat-value" id="distanceTraveled">0 km</div>
            </div>
        </div>

        <div class="stat-box">
            <div class="stat-icon" style="color: #17a2b8;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Active Time</div>
                <div class="stat-value" id="activeTime">0h 0m</div>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="map-column">
            <div class="section-card">
                <div class="section-header">
                    <h3><i class="fa-solid fa-map"></i> My Location Map</h3>
                    <div class="header-controls">
                        <label class="filter-label">
                            <span>Last</span>
                            <select id="timeFilter" class="time-filter">
                                <option value="1">1 Hour</option>
                                <option value="4">4 Hours</option>
                                <option value="8">8 Hours</option>
                                <option value="24" selected>24 Hours</option>
                            </select>
                        </label>
                    </div>
                </div>
                <div id="myLocationMap" class="location-map"></div>
            </div>
        </div>

        <div class="info-column">
            <div class="section-card">
                <div class="section-header">
                    <h3><i class="fa-solid fa-info-circle"></i> Current Location</h3>
                </div>
                <div class="location-info" id="locationInfo">
                    <div class="info-item">
                        <span class="label">Latitude:</span>
                        <span class="value" id="infoLat">--</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Longitude:</span>
                        <span class="value" id="infoLon">--</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Accuracy:</span>
                        <span class="value" id="infoAccuracy">--</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Address:</span>
                        <span class="value" id="infoAddress">--</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Last Update:</span>
                        <span class="value" id="infoTime">--</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Tracking Status:</span>
                        <div style="margin-top: 8px;">
                            <span id="trackingBadge" class="badge badge-offline">
                                <i class="fa-solid fa-circle" style="font-size: 8px; margin-right: 6px;"></i>
                                Offline
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h3><i class="fa-solid fa-rocket"></i> Quick Actions</h3>
                </div>
                <div class="action-buttons">
                    <button class="btn-action" onclick="startTracking()">
                        <i class="fa-solid fa-play"></i> Start Tracking
                    </button>
                    <button class="btn-action btn-secondary" onclick="stopTracking()">
                        <i class="fa-solid fa-stop"></i> Stop Tracking
                    </button>
                    <button class="btn-action btn-secondary" onclick="refreshLocation()">
                        <i class="fa-solid fa-rotate-right"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h3><i class="fa-solid fa-list"></i> Recent Locations</h3>
                </div>
                <div class="recent-locations" id="recentLocations">
                    <div class="loading">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .enforcer-dashboard-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;
        background: #f8f9fa;
    }

    .dashboard-header {
        margin-bottom: 24px;
    }

    .dashboard-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 8px;
    }

    .dashboard-header p {
        color: #666;
        margin: 0;
        font-size: 14px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-box {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        gap: 16px;
        align-items: flex-start;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .stat-icon {
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 13px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a1a;
        margin-top: 4px;
    }

    .dashboard-content {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
    }

    .section-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .section-header {
        padding: 16px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .section-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .header-controls {
        display: flex;
        gap: 12px;
    }

    .filter-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #666;
    }

    .time-filter {
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
    }

    .location-map {
        width: 100%;
        height: 500px;
        background: #f0f0f0;
    }

    .location-info {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-item .label {
        font-size: 12px;
        color: #999;
        text-transform: uppercase;
        font-weight: 600;
    }

    .info-item .value {
        font-size: 14px;
        color: #333;
        word-break: break-all;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        width: fit-content;
    }

    .badge-online {
        background: #d4edda;
        color: #155724;
    }

    .badge-offline {
        background: #e2e3e5;
        color: #383d41;
    }

    .badge-on_break {
        background: #fff3cd;
        color: #856404;
    }

    .action-buttons {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-action {
        padding: 12px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-action:hover {
        background: #0056b3;
    }

    .btn-action.btn-secondary {
        background: #6c757d;
    }

    .btn-action.btn-secondary:hover {
        background: #545b62;
    }

    .recent-locations {
        padding: 16px;
        flex: 1;
        overflow-y: auto;
        max-height: 300px;
    }

    .location-item {
        padding: 10px;
        border: 1px solid #f0f0f0;
        border-radius: 6px;
        margin-bottom: 8px;
        font-size: 13px;
        background: #f9f9f9;
    }

    .location-item:last-child {
        margin-bottom: 0;
    }

    .location-time {
        color: #999;
        font-size: 12px;
    }

    .location-coords {
        color: #007bff;
        font-weight: 600;
        font-size: 12px;
        margin-top: 4px;
    }

    .loading {
        padding: 20px;
        text-align: center;
        color: #999;
    }

    .info-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    @media (max-width: 1200px) {
        .dashboard-content {
            grid-template-columns: 1fr;
        }

        .info-column {
            grid-column: 1;
        }
    }

    @media (max-width: 768px) {
        .dashboard-header {
            margin-bottom: 16px;
        }

        .dashboard-header h1 {
            font-size: 22px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .location-map {
            height: 350px;
        }
    }
</style>

<script>
    let map = null;
    let myLocationMarkers = [];
    let polyline = null;

    function initMap() {
        map = L.map('myLocationMap').setView([14.5995, 121.0012], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        loadLocationHistory();
    }

    function loadLocationHistory() {
        const hours = document.getElementById('timeFilter').value || 24;
        
        fetch(`/gps/location-history/{{ auth()->user()->id }}?hours=${hours}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.locations.length > 0) {
                    displayLocations(data.locations);
                    updateStatistics(data.locations);
                }
            })
            .catch(error => console.error('Error loading history:', error));
    }

    function displayLocations(locations) {
        // Clear existing markers
        myLocationMarkers.forEach(marker => map.removeLayer(marker));
        myLocationMarkers = [];

        if (polyline) map.removeLayer(polyline);

        // Create route line
        const latlngs = locations.map(loc => [loc.latitude, loc.longitude]);
        polyline = L.polyline(latlngs, {
            color: '#007bff',
            weight: 3,
            opacity: 0.7,
            smoothFactor: 1
        }).addTo(map);

        // Add markers
        locations.forEach((location, index) => {
            const isStart = index === 0;
            const isEnd = index === locations.length - 1;

            let color = '#007bff';
            let icon = '📍';
            
            if (isStart) {
                color = '#28a745';
                icon = '🟢';
            } else if (isEnd) {
                color = '#dc3545';
                icon = '🔴';
            }

            const marker = L.circleMarker([location.latitude, location.longitude], {
                radius: 6,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).bindPopup(`
                <div style="font-size: 12px;">
                    <strong>${new Date(location.created_at).toLocaleTimeString()}</strong><br>
                    Lat: ${location.latitude.toFixed(6)}<br>
                    Lon: ${location.longitude.toFixed(6)}<br>
                    Accuracy: ±${location.accuracy_meters}m
                </div>
            `).addTo(map);

            myLocationMarkers.push(marker);
        });

        // Fit map to bounds
        if (latlngs.length > 0) {
            const bounds = L.latLngBounds(latlngs);
            map.fitBounds(bounds, { padding: [50, 50] });
        }

        // Show recent locations
        showRecentLocations(locations.slice(-10).reverse());
    }

    function updateStatistics(locations) {
        const count = locations.length;
        document.getElementById('locationCount').textContent = count;

        // Calculate distance traveled
        let distance = 0;
        for (let i = 1; i < locations.length; i++) {
            distance += getDistance(
                locations[i-1].latitude, locations[i-1].longitude,
                locations[i].latitude, locations[i].longitude
            );
        }
        document.getElementById('distanceTraveled').textContent = distance.toFixed(2) + ' km';

        // Calculate active time
        if (locations.length > 1) {
            const start = new Date(locations[0].created_at);
            const end = new Date(locations[locations.length - 1].created_at);
            const hours = Math.floor((end - start) / 3600000);
            const minutes = Math.floor(((end - start) % 3600000) / 60000);
            document.getElementById('activeTime').textContent = `${hours}h ${minutes}m`;
        }
    }

    function showRecentLocations(locations) {
        const container = document.getElementById('recentLocations');
        container.innerHTML = locations.map(loc => `
            <div class="location-item">
                <div class="location-time">${new Date(loc.created_at).toLocaleString()}</div>
                <div class="location-coords">${loc.latitude.toFixed(4)}, ${loc.longitude.toFixed(4)}</div>
                <div style="color: #999; font-size: 11px; margin-top: 4px;">Accuracy: ±${loc.accuracy_meters}m</div>
            </div>
        `).join('');
    }

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function getCurrentLocation() {
        fetch(`/gps/current-location`)
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    updateLocationInfo(data);
                    updateTrackingBadge(data.status);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function updateLocationInfo(location) {
        document.getElementById('infoLat').textContent = location.latitude.toFixed(8);
        document.getElementById('infoLon').textContent = location.longitude.toFixed(8);
        document.getElementById('infoAccuracy').textContent = `±${location.accuracy_meters}m`;
        document.getElementById('infoAddress').textContent = location.address || 'Not available';
        document.getElementById('infoTime').textContent = new Date(location.created_at).toLocaleString();
    }

    function updateTrackingBadge(status) {
        const badge = document.getElementById('trackingBadge');
        const statusMap = {
            'online': { class: 'badge-online', text: 'Online' },
            'offline': { class: 'badge-offline', text: 'Offline' },
            'on_break': { class: 'badge-on_break', text: 'On Break' }
        };
        const info = statusMap[status] || statusMap['offline'];
        badge.className = `badge ${info.class}`;
        badge.innerHTML = `<i class="fa-solid fa-circle" style="font-size: 8px; margin-right: 6px;"></i>${info.text}`;
    }

    function startTracking() {
        if (typeof LocationTracker !== 'undefined') {
            LocationTracker.start();
            alert('Location tracking started!');
        } else {
            alert('Location tracker not available');
        }
    }

    function stopTracking() {
        if (typeof LocationTracker !== 'undefined') {
            LocationTracker.stop();
            alert('Location tracking stopped!');
        }
    }

    function refreshLocation() {
        loadLocationHistory();
        getCurrentLocation();
    }

    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        getCurrentLocation();
        setInterval(getCurrentLocation, 10000);

        document.getElementById('timeFilter').addEventListener('change', loadLocationHistory);
    });
</script>
@endsection
