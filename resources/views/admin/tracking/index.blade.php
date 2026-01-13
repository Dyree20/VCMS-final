@extends('layouts.app')

@section('title', 'Enforcer Tracking')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

@section('content')
<div class="tracking-container">
    <div class="tracking-header">
        <h1><i class="fa-solid fa-location-dot"></i> Enforcer Tracking</h1>
        <p>Real-time visibility of all online enforcers</p>
    </div>

    <div class="tracking-stats">
        <div class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-circle" style="color: #28a745;"></i></div>
            <div>
                <div class="stat-value" id="onlineCount">0</div>
                <div class="stat-label">Online Now</div>
            </div>
        </div>
    </div>

    <div class="tracking-controls">
        <input type="text" id="searchInput" placeholder="Search enforcer..." class="search-input">
        <select id="sortBy" class="sort-select">
            <option value="recent">Sort by Recent</option>
            <option value="name">Sort by Name</option>
            <option value="accuracy">Sort by Accuracy</option>
        </select>
        <button id="refreshBtn" class="refresh-btn"><i class="fa-solid fa-rotate-right" style="margin-right: 6px;"></i>Refresh</button>
    </div>

    <div class="tracking-content">
        <!-- Enforcers Cards Column -->
        <div class="enforcers-column">
            <div class="enforcers-grid" id="enforcersContainer">
                <div class="loading">Loading enforcers...</div>
            </div>

            <div id="noDataState" class="no-data" style="display: none;">
                <div class="no-data-content">
                    <p style="font-size: 48px; margin: 0;"><i class="fa-solid fa-location-dot" style="font-size: 48px;"></i></p>
                    <h3>No Online Enforcers</h3>
                    <p>No enforcers are currently sharing their location.</p>
                </div>
            </div>
        </div>

        <!-- Map Column -->
        <div class="map-column">
            <div id="enforcerMap"></div>
        </div>
    </div>
</div>

@php
    $trackUrlBase = url('/tracking/enforcer');
@endphp

<style>
    .tracking-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;
    }
    .tracking-header {
        margin-bottom: 28px;
    }
    .tracking-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #333;
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
        margin-bottom: 28px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        gap: 16px;
        align-items: center;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }
    .stat-icon {
        font-size: 32px;
    }
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #4caf50;
    }
    .stat-label {
        font-size: 13px;
        color: #666;
    }
    .tracking-controls {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .search-input {
        flex: 1;
        min-width: 220px;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 10px;
        font-size: 14px;
    }
    .search-input:focus {
        outline: none;
        border-color: #2b58ff;
        box-shadow: 0 0 0 3px rgba(43, 88, 255, 0.1);
    }
    .sort-select {
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
    }
    .refresh-btn {
        padding: 10px 16px;
        border: none;
        border-radius: 10px;
        background: #2b58ff;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .refresh-btn:hover {
        background: #1f43cc;
    }

    /* Two-column layout */
    .tracking-content {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 24px;
        min-height: 700px;
    }

    .enforcers-column {
        display: flex;
        flex-direction: column;
    }

    .enforcers-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
        overflow-y: auto;
        padding-right: 8px;
        max-height: calc(100vh - 400px);
    }

    .enforcers-grid::-webkit-scrollbar {
        width: 6px;
    }

    .enforcers-grid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .enforcers-grid::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .enforcers-grid::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .enforcer-card {
        background: #fff;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        transition: all 0.2s;
        cursor: pointer;
    }

    .enforcer-card:hover {
        transform: translateX(-4px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        border-color: #2b58ff;
    }

    .enforcer-card.highlighted {
        background: linear-gradient(135deg, #e3f2fd 0%, #e8eaf6 100%);
        border-color: #2b58ff;
        box-shadow: 0 0 0 3px rgba(43, 88, 255, 0.15);
        transform: translateX(-4px);
    }

    .enforcer-header {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 10px;
    }
    .enforcer-name {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
    }
    .enforcer-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .status-online {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .status-on_break {
        background: #fff3e0;
        color: #ed6900;
    }
    .enforcer-info {
        font-size: 12px;
        color: #4b5563;
        line-height: 1.4;
    }
    .info-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }
    .info-label {
        font-weight: 600;
        min-width: 70px;
        color: #374151;
    }
    .location-badges {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #f0f0f0;
    }
    .badge {
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        background: #f3f4f6;
        color: #4b5563;
        white-space: nowrap;
    }
    .badge-accuracy {
        background: #e3f2fd;
        color: #1d4ed8;
    }

    /* Map column */
    .map-column {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }

    #enforcerMap {
        width: 100%;
        height: 100%;
        min-height: 700px;
    }

    .loading, .no-data {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
    }

    .no-data {
        grid-column: 1 / -1;
    }

    .no-data-content p {
        margin: 0;
    }

    @media (max-width: 1200px) {
        .tracking-content {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        #enforcerMap {
            min-height: 500px;
        }

        .enforcers-grid {
            max-height: 400px;
        }
    }

    @media (max-width: 768px) {
        .tracking-controls {
            flex-direction: column;
        }
        .search-input, .sort-select, .refresh-btn {
            width: 100%;
        }
        
        .tracking-content {
            grid-template-columns: 1fr;
        }

        #enforcerMap {
            min-height: 400px;
        }

        .enforcer-card {
            padding: 12px;
        }

        .enforcer-name {
            font-size: 14px;
        }
    }
</style>
<script>
    class EnforcerTrackingDashboard {
        constructor() {
            this.container = document.getElementById('enforcersContainer');
            this.searchInput = document.getElementById('searchInput');
            this.sortBy = document.getElementById('sortBy');
            this.refreshBtn = document.getElementById('refreshBtn');
            this.onlineCount = document.getElementById('onlineCount');
            this.noDataState = document.getElementById('noDataState');
            this.mapContainer = document.getElementById('enforcerMap');
            this.historyBaseUrl = "{{ $trackUrlBase }}";

            this.enforcers = [];
            this.filteredEnforcers = [];
            this.map = null;
            this.markers = {};
            this.selectedEnforcer = null;

            this.bindEvents();
            this.initMap();
            this.loadData();
            // Refresh data every 10 seconds for real-time updates
            setInterval(() => this.loadData(), 10000);
        }

        initMap() {
            // Initialize Leaflet map centered on Manila
            this.map = L.map(this.mapContainer).setView([14.5995, 121.0012], 13);
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(this.map);

            // Enable scroll wheel zoom
            this.map.scrollWheelZoom.enable();
        }

        bindEvents() {
            this.searchInput.addEventListener('input', () => this.filter());
            this.sortBy.addEventListener('change', () => this.filter());
            this.refreshBtn.addEventListener('click', () => this.loadData());
        }

        loadData() {
            this.container.innerHTML = '<div class="loading">Loading enforcers...</div>';
            fetch('{{ route('gps.online-enforcers') }}')
                .then(res => res.json())
                .then(data => {
                    this.enforcers = data.enforcers || [];
                    this.onlineCount.textContent = this.enforcers.length;
                    this.updateMap();
                    this.filter();
                })
                .catch(() => {
                    this.container.innerHTML = '<div class="loading">Failed to load data</div>';
                });
        }

        updateMap() {
            // Clear existing markers
            Object.values(this.markers).forEach(marker => this.map.removeLayer(marker));
            this.markers = {};

            if (!this.enforcers.length) return;

            // Add markers for all enforcers
            this.enforcers.forEach(enforcer => {
                const marker = L.circleMarker([enforcer.latitude, enforcer.longitude], {
                    radius: 8,
                    fillColor: '#2b58ff',
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8,
                    className: `enforcer-marker-${enforcer.user_id}`
                }).addTo(this.map);

                // Add popup with enforcer info
                const name = `${enforcer.user.f_name} ${enforcer.user.l_name}`;
                marker.bindPopup(`
                    <div style="font-weight: 600; margin-bottom: 6px;">${name}</div>
                    <div style="font-size: 12px; color: #666;">
                        <div>Status: <span style="color: #28a745; font-weight: 600;">${enforcer.status.replace('_', ' ')}</span></div>
                        <div>Accuracy: ${enforcer.accuracy_meters}m</div>
                    </div>
                `);

                this.markers[enforcer.user_id] = marker;
            });

            // Auto-fit map to all markers
            if (Object.keys(this.markers).length > 0) {
                const group = new L.featureGroup(Object.values(this.markers));
                this.map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        highlightEnforcer(enforcerId) {
            // Remove previous highlight
            if (this.selectedEnforcer) {
                const previousCard = document.querySelector(`[data-enforcer-id="${this.selectedEnforcer}"]`);
                if (previousCard) previousCard.classList.remove('highlighted');
                
                const previousMarker = this.markers[this.selectedEnforcer];
                if (previousMarker) {
                    previousMarker.setRadius(8);
                    previousMarker.setStyle({ fillColor: '#2b58ff', weight: 2 });
                }
            }

            this.selectedEnforcer = enforcerId;

            // Highlight new card
            const card = document.querySelector(`[data-enforcer-id="${enforcerId}"]`);
            if (card) card.classList.add('highlighted');

            // Highlight new marker
            const marker = this.markers[enforcerId];
            if (marker) {
                marker.setRadius(12);
                marker.setStyle({ fillColor: '#ff6b6b', weight: 3 });
                this.map.panTo(marker.getLatLng());
            }
        }

        filter() {
            const query = this.searchInput.value.toLowerCase();
            this.filteredEnforcers = this.enforcers.filter(item => {
                const fullName = `${item.user.f_name ?? ''} ${item.user.l_name ?? ''}`.trim().toLowerCase();
                return fullName.includes(query);
            });

            const sort = this.sortBy.value;
            if (sort === 'recent') {
                this.filteredEnforcers.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            } else if (sort === 'name') {
                this.filteredEnforcers.sort((a, b) => (`${a.user.f_name} ${a.user.l_name}`).localeCompare(`${b.user.f_name} ${b.user.l_name}`));
            } else if (sort === 'accuracy') {
                this.filteredEnforcers.sort((a, b) => (a.accuracy_meters || 999) - (b.accuracy_meters || 999));
            }

            this.render();
        }

        render() {
            if (!this.filteredEnforcers.length) {
                this.container.style.display = 'none';
                this.noDataState.style.display = 'block';
                return;
            }

            this.container.style.display = 'flex';
            this.noDataState.style.display = 'none';

            this.container.innerHTML = this.filteredEnforcers.map(enforcer => `
                <div class="enforcer-card" data-enforcer-id="${enforcer.user_id}">
                    <div class="enforcer-header">
                        <h3 class="enforcer-name">${enforcer.user.f_name} ${enforcer.user.l_name}</h3>
                        <span class="enforcer-status status-${enforcer.status}">
                            ${enforcer.status.replace('_', ' ')}
                        </span>
                    </div>
                    <div class="enforcer-info">
                        <div class="info-row">
                            <span class="info-label">Lat:</span>
                            <span>${Number(enforcer.latitude).toFixed(4)}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Lng:</span>
                            <span>${Number(enforcer.longitude).toFixed(4)}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Updated:</span>
                            <span>${this.formatTime(enforcer.created_at)}</span>
                        </div>
                    </div>
                    <div class="location-badges">
                        <span class="badge badge-accuracy"><i class="fa-solid fa-bullseye" style="margin-right: 4px;"></i>${enforcer.accuracy_meters || 'N/A'}m</span>
                    </div>
                </div>
            `).join('');

            // Attach click handlers to cards
            this.filteredEnforcers.forEach(enforcer => {
                const card = document.querySelector(`[data-enforcer-id="${enforcer.user_id}"]`);
                if (card) {
                    card.addEventListener('click', () => this.highlightEnforcer(enforcer.user_id));
                }
            });
        }

        formatTime(ts) {
            const date = new Date(ts);
            const diffMinutes = Math.floor((Date.now() - date.getTime()) / 60000);
            if (diffMinutes < 1) return 'Just now';
            if (diffMinutes < 60) return `${diffMinutes}m ago`;
            const hours = Math.floor(diffMinutes / 60);
            if (hours < 24) return `${hours}h ago`;
            return date.toLocaleDateString();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        new EnforcerTrackingDashboard();
    });
</script>
@endsection

