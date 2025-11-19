@extends('layouts.app')

@section('title', 'Enforcer GPS Tracking')

@section('content')
<div class="tracking-container">
    <!-- Header -->
    <div class="tracking-header">
        <h1>📍 Enforcer GPS Tracking</h1>
        <p>Real-time location tracking of online enforcers</p>
    </div>

    <!-- Stats Cards -->
    <div class="tracking-stats">
        <div class="stat-card">
            <div class="stat-icon">🟢</div>
            <div class="stat-info">
                <div class="stat-value" id="onlineCount">0</div>
                <div class="stat-label">Online Now</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="tracking-controls">
        <input type="text" id="searchInput" placeholder="Search enforcer by name..." class="search-input">
        <select id="sortBy" class="sort-select">
            <option value="recent">Sort by Recent</option>
            <option value="name">Sort by Name</option>
            <option value="accuracy">Sort by Accuracy</option>
        </select>
        <button id="refreshBtn" class="refresh-btn">🔄 Refresh Now</button>
    </div>

    <!-- Online Enforcers List -->
    <div class="enforcers-grid" id="enforcersContainer">
        <div class="loading">Loading enforcers...</div>
    </div>

    <!-- No Data State -->
    <div id="noDataState" class="no-data" style="display: none;">
        <div class="no-data-content">
            <p style="font-size: 48px; margin: 0;">📍</p>
            <h3>No Online Enforcers</h3>
            <p>No enforcers are currently tracking their location.</p>
        </div>
    </div>
</div>

<style>
    .tracking-container {
        max-width: 1200px;
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
        margin: 0 0 8px 0;
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
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .stat-icon {
        font-size: 32px;
    }

    .stat-info {
        flex: 1;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #4caf50;
        margin: 0;
    }

    .stat-label {
        font-size: 13px;
        color: #666;
        margin: 4px 0 0 0;
    }

    .tracking-controls {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
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
        border-radius: 6px;
        font-size: 14px;
        background: white;
        cursor: pointer;
    }

    .sort-select:focus {
        outline: none;
        border-color: #2b58ff;
    }

    .refresh-btn {
        padding: 10px 16px;
        background: #2b58ff;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .refresh-btn:hover {
        background: #1e42cc;
        transform: translateY(-1px);
    }

    .refresh-btn:active {
        transform: translateY(0);
    }

    .enforcers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 16px;
    }

    .enforcer-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 16px;
        transition: all 0.3s;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .enforcer-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .enforcer-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        gap: 12px;
    }

    .enforcer-name {
        font-size: 16px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    .enforcer-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 16px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-online {
        background: #e8f5e9;
        color: #4caf50;
    }

    .status-on-break {
        background: #fff3e0;
        color: #ff9800;
    }

    .enforcer-info {
        font-size: 13px;
        color: #666;
        line-height: 1.6;
    }

    .info-row {
        display: flex;
        align-items: center;
        margin: 8px 0;
        gap: 8px;
    }

    .info-label {
        font-weight: 600;
        color: #333;
        min-width: 80px;
    }

    .info-value {
        color: #666;
    }

    .location-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        background: #f0f0f0;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-accuracy {
        background: #e3f2fd;
        color: #2b58ff;
    }

    .enforcer-actions {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
    }

    .action-link {
        display: inline-block;
        padding: 6px 12px;
        background: #e3f2fd;
        color: #2b58ff;
        text-decoration: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .action-link:hover {
        background: #2b58ff;
        color: white;
    }

    .no-data {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
    }

    .no-data-content {
        color: #999;
    }

    .no-data-content h3 {
        font-size: 18px;
        margin: 12px 0 8px 0;
        color: #333;
    }

    .loading {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        color: #999;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .tracking-container {
            padding: 16px;
        }

        .tracking-header h1 {
            font-size: 22px;
        }

        .tracking-controls {
            flex-direction: column;
        }

        .search-input,
        .sort-select {
            width: 100%;
        }

        .enforcers-grid {
            grid-template-columns: 1fr;
        }

        .enforcer-header {
            flex-direction: column;
        }
    }
</style>

<script>
    class GPSTrackingDashboard {
        constructor() {
            this.container = document.getElementById('enforcersContainer');
            this.searchInput = document.getElementById('searchInput');
            this.sortBy = document.getElementById('sortBy');
            this.refreshBtn = document.getElementById('refreshBtn');
            this.onlineCount = document.getElementById('onlineCount');
            this.noDataState = document.getElementById('noDataState');
            
            this.enforcers = [];
            this.filteredEnforcers = [];
            
            this.init();
        }

        init() {
            this.loadOnlineEnforcers();
            this.setupEventListeners();
            
            // Auto-refresh every 30 seconds
            setInterval(() => this.loadOnlineEnforcers(), 30000);
        }

        setupEventListeners() {
            this.searchInput.addEventListener('input', () => this.filterAndSort());
            this.sortBy.addEventListener('change', () => this.filterAndSort());
            this.refreshBtn.addEventListener('click', () => this.loadOnlineEnforcers());
        }

        loadOnlineEnforcers() {
            this.container.innerHTML = '<div class="loading">Loading enforcers...</div>';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch('/gps/online-enforcers', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                this.enforcers = data.enforcers || [];
                this.onlineCount.textContent = this.enforcers.length;
                this.filterAndSort();
            })
            .catch(error => {
                console.error('Error loading enforcers:', error);
                this.container.innerHTML = '<div class="loading">Failed to load enforcers</div>';
            });
        }

        filterAndSort() {
            this.filteredEnforcers = this.enforcers.filter(enforcer => {
                const fullName = `${enforcer.user.f_name} ${enforcer.user.l_name}`.toLowerCase();
                const searchTerm = this.searchInput.value.toLowerCase();
                return fullName.includes(searchTerm);
            });

            // Sort
            const sortValue = this.sortBy.value;
            if (sortValue === 'recent') {
                this.filteredEnforcers.sort((a, b) => 
                    new Date(b.created_at) - new Date(a.created_at)
                );
            } else if (sortValue === 'name') {
                this.filteredEnforcers.sort((a, b) => 
                    `${a.user.f_name} ${a.user.l_name}`.localeCompare(`${b.user.f_name} ${b.user.l_name}`)
                );
            } else if (sortValue === 'accuracy') {
                this.filteredEnforcers.sort((a, b) => 
                    (a.accuracy_meters || 999) - (b.accuracy_meters || 999)
                );
            }

            this.render();
        }

        render() {
            if (this.filteredEnforcers.length === 0) {
                this.container.style.display = 'none';
                this.noDataState.style.display = 'block';
                return;
            }

            this.container.style.display = 'grid';
            this.noDataState.style.display = 'none';
            
            this.container.innerHTML = this.filteredEnforcers.map(enforcer => `
                <div class="enforcer-card">
                    <div class="enforcer-header">
                        <h3 class="enforcer-name">${enforcer.user.f_name} ${enforcer.user.l_name}</h3>
                        <span class="enforcer-status status-${enforcer.status}">
                            ${enforcer.status.replace('_', ' ')}
                        </span>
                    </div>
                    
                    <div class="enforcer-info">
                        <div class="info-row">
                            <span class="info-label">📍 Latitude:</span>
                            <span class="info-value">${enforcer.latitude.toFixed(6)}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">📍 Longitude:</span>
                            <span class="info-value">${enforcer.longitude.toFixed(6)}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">⏱️ Updated:</span>
                            <span class="info-value">${this.formatTime(enforcer.created_at)}</span>
                        </div>
                    </div>

                    <div class="location-badges">
                        <span class="badge badge-accuracy">
                            🎯 Accuracy: ${enforcer.accuracy_meters || 'N/A'}m
                        </span>
                        ${enforcer.address ? `<span class="badge">${enforcer.address}</span>` : ''}
                    </div>

                    <div class="enforcer-actions">
                        <a href="/tracking/enforcer/${enforcer.user_id}" class="action-link">
                            View History
                        </a>
                    </div>
                </div>
            `).join('');
        }

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            
            if (minutes < 1) return 'Just now';
            if (minutes < 60) return `${minutes}m ago`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours}h ago`;
            return date.toLocaleDateString();
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        new GPSTrackingDashboard();
    });
</script>
@endsection
