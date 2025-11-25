@extends('layouts.app')

@section('title', 'Enforcer Tracking')

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

@php
    $trackUrlBase = url('/tracking/enforcer');
@endphp

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
    .enforcers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 16px;
    }
    .enforcer-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 18px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        transition: 0.2s;
    }
    .enforcer-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        border-color: #2b58ff;
    }
    .enforcer-header {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 12px;
    }
    .enforcer-name {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }
    .enforcer-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
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
        font-size: 13px;
        color: #4b5563;
        line-height: 1.5;
    }
    .info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }
    .info-label {
        font-weight: 600;
        min-width: 80px;
        color: #374151;
    }
    .location-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 12px;
        border-top: 1px solid #f0f0f0;
        padding-top: 12px;
    }
    .badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        background: #f3f4f6;
        color: #4b5563;
    }
    .badge-accuracy {
        background: #e3f2fd;
        color: #1d4ed8;
    }
    .enforcer-actions {
        margin-top: 14px;
    }
    .action-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4338ca;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
    }
    .action-link:hover {
        background: #4338ca;
        color: #fff;
    }
    .loading, .no-data {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        color: #9ca3af;
    }
    @media (max-width: 768px) {
        .tracking-controls {
            flex-direction: column;
        }
        .search-input, .sort-select, .refresh-btn {
            width: 100%;
        }
        .enforcers-grid {
            grid-template-columns: 1fr;
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
            this.historyBaseUrl = "{{ $trackUrlBase }}";

            this.enforcers = [];
            this.filteredEnforcers = [];

            this.bindEvents();
            this.loadData();
            setInterval(() => this.loadData(), 30000);
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
                    this.filter();
                })
                .catch(() => {
                    this.container.innerHTML = '<div class="loading">Failed to load data</div>';
                });
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
                            <span class="info-label">Latitude:</span>
                            <span>${Number(enforcer.latitude).toFixed(6)}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Longitude:</span>
                            <span>${Number(enforcer.longitude).toFixed(6)}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Updated:</span>
                            <span>${this.formatTime(enforcer.created_at)}</span>
                        </div>
                    </div>
                    <div class="location-badges">
                        <span class="badge badge-accuracy"><i class="fa-solid fa-bullseye" style="margin-right: 6px;"></i>Accuracy: ${enforcer.accuracy_meters || 'N/A'}m</span>
                        ${enforcer.address ? `<span class="badge">${enforcer.address}</span>` : ''}
                    </div>
                    <div class="enforcer-actions">
                        <a href="${this.historyBaseUrl}/${enforcer.user_id}" class="action-link">
                            View Movement History
                        </a>
                    </div>
                </div>
            `).join('');
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

