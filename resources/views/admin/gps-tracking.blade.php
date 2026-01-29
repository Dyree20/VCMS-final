@extends('layouts.app')

@section('title', 'Real-Time Vehicle Tracking')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

@section('content')
<div class="vehicle-tracking-container">
    <div class="tracking-header">
        <h1><i class="fa-solid fa-car-side"></i> Real-Time Vehicle Tracking</h1>
        <p>Live tracking of clamping operations across the city</p>
    </div>

    <div class="tracking-stats">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-circle" style="color: #28a745;"></i></div>
            <div>
                <div class="stat-value" id="activeEnforcersCount">0</div>
                <div class="stat-label">Active Officers</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-location-dot" style="color: #007bff;"></i></div>
            <div>
                <div class="stat-value" id="operationsCount">0</div>
                <div class="stat-label">Active Operations</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-clock" style="color: #ffc107;"></i></div>
            <div>
                <div class="stat-value" id="lastUpdateTime">--:--</div>
                <div class="stat-label">Last Update</div>
            </div>
        </div>
    </div>

    <div class="tracking-controls">
        <div class="control-group">
            <input type="text" id="searchInput" placeholder="Search officer..." class="search-input">
            <select id="filterStatus" class="filter-select">
                <option value="">All Status</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                <option value="on_break">On Break</option>
            </select>
        </div>
        <div class="control-group">
            <label>
                <input type="checkbox" id="autoRefresh" checked>
                <span>Auto Refresh (5s)</span>
            </label>
            <button id="refreshBtn" class="btn-refresh"><i class="fa-solid fa-rotate-right"></i> Refresh Now</button>
        </div>
    </div>

    <div class="tracking-layout">
        <!-- Map Section -->
        <div class="map-section">
            <div id="vehicleMap" class="vehicle-map"></div>
            <div class="map-legend">
                <div class="legend-item">
                    <span class="legend-color" style="background: #28a745;"></span>
                    <span>Online</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color" style="background: #6c757d;"></span>
                    <span>Offline</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color" style="background: #ffc107;"></span>
                    <span>On Break</span>
                </div>
            </div>
        </div>

        <!-- Officers Panel -->
        <div class="officers-panel">
            <div class="panel-header">
                <h3><i class="fa-solid fa-users"></i> Active Officers</h3>
                <span class="officer-count" id="officersListCount">0</span>
            </div>
            <div class="officers-list" id="officersContainer">
                <div class="loading-state">
                    <i class="fa-solid fa-spinner fa-spin"></i> Loading officers...
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .vehicle-tracking-container {
        max-width: 1800px;
        margin: 0 auto;
        padding: 20px;
        background: #f8f9fa;
    }

    .tracking-header {
        margin-bottom: 24px;
    }

    .tracking-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 8px;
    }

    .tracking-header p {
        color: #666;
        margin: 0;
        font-size: 14px;
    }

    .tracking-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        gap: 16px;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        border-color: #007bff;
    }

    .stat-icon {
        font-size: 32px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .stat-label {
        font-size: 13px;
        color: #999;
        margin-top: 4px;
    }

    .tracking-controls {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        flex-wrap: wrap;
    }

    .control-group {
        display: flex;
        gap: 12px;
        align-items: center;
        flex: 1;
        min-width: 300px;
    }

    .search-input,
    .filter-select {
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .search-input {
        flex: 1;
        min-width: 200px;
    }

    .search-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }

    .filter-select {
        min-width: 150px;
    }

    .btn-refresh {
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-refresh:hover {
        background: #0056b3;
    }

    .btn-refresh:active {
        transform: scale(0.98);
    }

    .tracking-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
    }

    .map-section {
        position: relative;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .vehicle-map {
        width: 100%;
        height: 600px;
        border-radius: 12px;
    }

    .map-legend {
        position: absolute;
        bottom: 20px;
        left: 20px;
        background: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        z-index: 1000;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        margin: 4px 0;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
    }

    .officers-panel {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .panel-header {
        padding: 16px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .officer-count {
        background: #007bff;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .officers-list {
        flex: 1;
        overflow-y: auto;
        max-height: 600px;
    }

    .officer-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .officer-item:hover {
        background: #f8f9fa;
    }

    .officer-item.active {
        background: #e7f3ff;
        border-left: 3px solid #007bff;
    }

    .officer-status-badge {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .officer-status-badge.online {
        background: #28a745;
    }

    .officer-status-badge.offline {
        background: #6c757d;
    }

    .officer-status-badge.on_break {
        background: #ffc107;
    }

    .officer-info {
        flex: 1;
        min-width: 0;
    }

    .officer-name {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .officer-details {
        font-size: 12px;
        color: #999;
        margin-top: 2px;
    }

    .officer-distance {
        font-size: 12px;
        color: #007bff;
        font-weight: 600;
    }

    .loading-state {
        padding: 40px 20px;
        text-align: center;
        color: #999;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .loading-state i {
        font-size: 24px;
        color: #ddd;
    }

    .no-data-state {
        padding: 40px 20px;
        text-align: center;
        color: #999;
    }

    .no-data-state p {
        margin: 0;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .tracking-layout {
            grid-template-columns: 1fr;
        }

        .vehicle-map {
            height: 500px;
        }

        .officers-panel {
            max-height: 400px;
        }
    }

    @media (max-width: 768px) {
        .tracking-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .control-group {
            flex-direction: column;
            min-width: auto;
        }

        .search-input,
        .filter-select {
            width: 100%;
        }

        .vehicle-map {
            height: 400px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('vehicleMap').setView([14.5995, 121.0012], 13); // Manila coordinates
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    const markers = {};
    let autoRefreshInterval = null;
    let currentEnforcers = [];

    // Status colors
    const statusColors = {
        'online': '#28a745',
        'offline': '#6c757d',
        'on_break': '#ffc107'
    };

    function updateLastTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('lastUpdateTime').textContent = `${hours}:${minutes}`;
    }

    function createMarkerPopup(enforcer) {
        const status = enforcer.status || 'offline';
        const accuracy = enforcer.accuracy_meters ? `±${enforcer.accuracy_meters}m` : 'N/A';
        
        return `
            <div class="marker-popup" style="font-family: Arial; font-size: 12px;">
                <div style="font-weight: 600; margin-bottom: 6px; color: #1a1a1a;">
                    ${enforcer.user?.f_name} ${enforcer.user?.l_name}
                </div>
                <div style="margin: 4px 0; color: #666;">
                    <strong>Status:</strong> <span style="color: ${statusColors[status]}; font-weight: 600;">${status.toUpperCase()}</span>
                </div>
                <div style="margin: 4px 0; color: #666;">
                    <strong>Accuracy:</strong> ${accuracy}
                </div>
                <div style="margin: 4px 0; color: #666;">
                    <strong>Location:</strong> ${enforcer.address || 'Not available'}
                </div>
                <div style="margin: 4px 0; color: #999; font-size: 11px;">
                    Updated: ${new Date(enforcer.created_at).toLocaleTimeString()}
                </div>
            </div>
        `;
    }

    function updateMap(enforcers) {
        currentEnforcers = enforcers;
        
        enforcers.forEach(enforcer => {
            const key = `enforcer_${enforcer.user_id}`;
            const status = enforcer.status || 'offline';
            const color = statusColors[status] || '#6c757d';

            if (markers[key]) {
                // Update existing marker
                markers[key].setLatLng([enforcer.latitude, enforcer.longitude]);
                markers[key].setPopupContent(createMarkerPopup(enforcer));
            } else {
                // Create new marker
                const markerColor = status === 'online' ? '#28a745' : status === 'on_break' ? '#ffc107' : '#6c757d';
                
                const customIcon = L.icon({
                    iconUrl: `data:image/svg+xml;base64,${btoa(`
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32">
                            <circle cx="16" cy="16" r="14" fill="${markerColor}" stroke="white" stroke-width="2"/>
                            <circle cx="16" cy="16" r="6" fill="white"/>
                        </svg>
                    `)}`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });

                const marker = L.marker([enforcer.latitude, enforcer.longitude], {
                    icon: customIcon,
                    title: `${enforcer.user?.f_name} ${enforcer.user?.l_name}`
                })
                .bindPopup(createMarkerPopup(enforcer))
                .addTo(map);

                markers[key] = marker;
            }
        });

        // Remove markers for enforcers no longer in list
        Object.keys(markers).forEach(key => {
            const enforcerId = parseInt(key.split('_')[1]);
            if (!enforcers.find(e => e.user_id === enforcerId)) {
                map.removeLayer(markers[key]);
                delete markers[key];
            }
        });
    }

    function updateOfficersList(enforcers) {
        const container = document.getElementById('officersContainer');
        
        if (enforcers.length === 0) {
            container.innerHTML = '<div class="no-data-state"><p>No officers online</p></div>';
            document.getElementById('officersListCount').textContent = '0';
            return;
        }

        const filtered = getFilteredEnforcers(enforcers);
        document.getElementById('officersListCount').textContent = filtered.length;

        container.innerHTML = filtered.map(enforcer => `
            <div class="officer-item" onclick="selectOfficer(${enforcer.user_id})">
                <div class="officer-status-badge ${enforcer.status || 'offline'}"></div>
                <div class="officer-info">
                    <div class="officer-name">${enforcer.user?.f_name} ${enforcer.user?.l_name}</div>
                    <div class="officer-details">
                        ${(enforcer.status || 'offline').toUpperCase()} • ${new Date(enforcer.created_at).toLocaleTimeString()}
                    </div>
                </div>
            </div>
        `).join('');
    }

    function getFilteredEnforcers(enforcers) {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('filterStatus').value;

        return enforcers.filter(enforcer => {
            const name = `${enforcer.user?.f_name} ${enforcer.user?.l_name}`.toLowerCase();
            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = !statusFilter || enforcer.status === statusFilter;
            return matchesSearch && matchesStatus;
        });
    }

    function fetchOnlineEnforcers() {
        fetch('{{ route("gps.online-enforcers") }}')
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                const enforcers = data.enforcers || [];
                
                document.getElementById('activeEnforcersCount').textContent = enforcers.length;
                document.getElementById('operationsCount').textContent = enforcers.length;
                
                updateMap(enforcers);
                updateOfficersList(enforcers);
                updateLastTime();
            })
            .catch(error => {
                console.error('Error fetching enforcers:', error);
            });
    }

    // Initial fetch
    fetchOnlineEnforcers();

    // Auto refresh
    document.getElementById('autoRefresh').addEventListener('change', function() {
        if (this.checked) {
            autoRefreshInterval = setInterval(fetchOnlineEnforcers, 5000);
        } else {
            if (autoRefreshInterval) clearInterval(autoRefreshInterval);
        }
    });

    document.getElementById('autoRefresh').checked = true;
    autoRefreshInterval = setInterval(fetchOnlineEnforcers, 5000);

    // Manual refresh
    document.getElementById('refreshBtn').addEventListener('click', fetchOnlineEnforcers);

    // Filter and search
    document.getElementById('searchInput').addEventListener('input', function() {
        updateOfficersList(currentEnforcers);
    });

    document.getElementById('filterStatus').addEventListener('change', function() {
        updateOfficersList(currentEnforcers);
    });

    window.selectOfficer = function(userId) {
        const key = `enforcer_${userId}`;
        if (markers[key]) {
            markers[key].openPopup();
            map.setView([markers[key].getLatLng().lat, markers[key].getLatLng().lng], 15);
        }
    };
});
</script>
@endsection
