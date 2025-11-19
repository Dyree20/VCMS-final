@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="notifications-container">
    <!-- Header -->
    <div class="notifications-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class='bx bx-bell'></i>
                Notifications
            </h1>
            <p class="page-subtitle">Stay updated with your activity</p>
        </div>
        <div class="header-actions">
            <button id="markAllReadBtn" class="btn-action mark-all-read" title="Mark all as read">
                <i class='bx bx-check-double'></i>
                <span>Mark All as Read</span>
            </button>
            <button id="clearAllBtn" class="btn-action clear-all" title="Clear all notifications">
                <i class='bx bx-trash'></i>
                <span>Clear All</span>
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="notifications-filter">
        <button class="filter-tab active" data-filter="all">
            <span>All</span>
            <span class="count" id="countAll">0</span>
        </button>
        <button class="filter-tab" data-filter="unread">
            <span>Unread</span>
            <span class="count" id="countUnread">0</span>
        </button>
        <button class="filter-tab" data-filter="clamping">
            <i class='bx bx-car'></i>
            <span>Clamping</span>
        </button>
        <button class="filter-tab" data-filter="payment">
            <i class='bx bx-wallet'></i>
            <span>Payment</span>
        </button>
        <button class="filter-tab" data-filter="appeal">
            <i class='bx bx-file'></i>
            <span>Appeal</span>
        </button>
        <button class="filter-tab" data-filter="system">
            <i class='bx bx-cog'></i>
            <span>System</span>
        </button>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list" id="notificationsList">
        <div class="loading-state">
            <i class='bx bx-loader-alt spinning'></i>
            <p>Loading notifications...</p>
        </div>
    </div>
</div>

<style>
    .notifications-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    /* Header Styles */
    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 28px 32px;
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: white;
        gap: 20px;
        flex-wrap: wrap;
    }

    .header-content h1 {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .header-content h1 i {
        font-size: 32px;
    }

    .page-subtitle {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
        font-weight: 500;
    }

    .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-action {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
    }

    .btn-action i {
        font-size: 16px;
    }

    /* Filter Tabs */
    .notifications-filter {
        display: flex;
        gap: 8px;
        padding: 16px 32px;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        overflow-x: auto;
        flex-wrap: wrap;
    }

    .filter-tab {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: #666;
        white-space: nowrap;
        transition: all 0.3s ease;
    }

    .filter-tab:hover {
        border-color: #2b58ff;
        color: #2b58ff;
        background: #f0f5ff;
    }

    .filter-tab.active {
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: white;
        border-color: #2b58ff;
    }

    .filter-tab .count {
        background: rgba(0, 0, 0, 0.1);
        padding: 2px 6px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
    }

    .filter-tab.active .count {
        background: rgba(255, 255, 255, 0.3);
    }

    .filter-tab i {
        font-size: 16px;
    }

    /* Notifications List */
    .notifications-list {
        min-height: 300px;
        max-height: 800px;
        overflow-y: auto;
    }

    .loading-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 32px;
        text-align: center;
        color: #999;
    }

    .loading-state i {
        font-size: 48px;
        margin-bottom: 16px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 80px 32px;
        text-align: center;
        color: #999;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    .empty-state p {
        font-size: 14px;
        margin: 0;
    }

    /* Notification Item */
    .notification-item {
        padding: 18px 32px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        gap: 16px;
        align-items: flex-start;
        position: relative;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item.unread {
        background: #f0f5ff;
        border-left: 4px solid #2b58ff;
        padding-left: 28px;
    }

    .notification-item.unread:hover {
        background: #e8f0ff;
    }

    .notification-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        background: #f0f0f0;
    }

    .notification-item.unread .notification-icon {
        background: #e8f0ff;
    }

    .notification-item.type-clamping .notification-icon {
        background: #fff3e0;
        color: #f57c00;
    }

    .notification-item.type-payment .notification-icon {
        background: #e8f5e9;
        color: #388e3c;
    }

    .notification-item.type-appeal .notification-icon {
        background: #e3f2fd;
        color: #1976d2;
    }

    .notification-item.type-system .notification-icon {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 6px;
    }

    .notification-title {
        font-weight: 700;
        font-size: 14px;
        color: #333;
        margin: 0;
    }

    .notification-type-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: capitalize;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .type-clamping {
        background: #fff3e0;
        color: #f57c00;
    }

    .type-payment {
        background: #e8f5e9;
        color: #388e3c;
    }

    .type-appeal {
        background: #e3f2fd;
        color: #1976d2;
    }

    .type-system {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .notification-message {
        color: #666;
        font-size: 13px;
        margin: 0 0 6px 0;
        line-height: 1.5;
    }

    .notification-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .notification-time {
        color: #999;
        font-size: 12px;
    }

    .notification-actions {
        display: flex;
        gap: 8px;
    }

    .notification-action-btn {
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 14px;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .notification-action-btn:hover {
        color: #2b58ff;
        background: #f0f5ff;
    }

    .notification-action-btn.delete:hover {
        color: #dc3545;
        background: #ffe8e8;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .notifications-header {
            padding: 24px 24px;
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .notification-item {
            padding: 16px 24px;
            padding-left: 20px;
        }

        .notification-item.unread {
            padding-left: 16px;
        }

        .notifications-filter {
            padding: 12px 24px;
        }
    }

    @media (max-width: 768px) {
        .notifications-header {
            padding: 20px 16px;
        }

        .header-content h1 {
            font-size: 22px;
        }

        .header-actions {
            width: 100%;
        }

        .btn-action {
            padding: 8px 12px;
            font-size: 12px;
        }

        .btn-action span {
            display: none;
        }

        .btn-action i {
            font-size: 14px;
        }

        .notifications-filter {
            padding: 10px 16px;
            gap: 6px;
        }

        .filter-tab {
            padding: 6px 10px;
            font-size: 12px;
        }

        .notification-item {
            padding: 14px 16px;
            padding-left: 12px;
            gap: 12px;
        }

        .notification-item.unread {
            padding-left: 12px;
            border-left-width: 3px;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }

        .notification-title {
            font-size: 13px;
        }

        .notification-message {
            font-size: 12px;
        }

        .notification-time {
            font-size: 11px;
        }

        .notifications-list {
            max-height: 600px;
        }
    }

    @media (max-width: 480px) {
        .notifications-header {
            padding: 16px 12px;
        }

        .header-content h1 {
            font-size: 18px;
            gap: 8px;
        }

        .header-content h1 i {
            font-size: 24px;
        }

        .page-subtitle {
            font-size: 12px;
        }

        .header-actions {
            gap: 8px;
        }

        .btn-action {
            padding: 6px 8px;
            font-size: 11px;
        }

        .notifications-filter {
            padding: 8px 12px;
            gap: 4px;
        }

        .filter-tab {
            padding: 5px 8px;
            font-size: 11px;
        }

        .filter-tab .count {
            display: none;
        }

        .filter-tab span:not(.count) {
            display: none;
        }

        .filter-tab i {
            font-size: 14px;
        }

        .filter-tab:only-of-type span:not(.count) {
            display: inline;
        }

        .notification-item {
            padding: 12px 12px;
            padding-left: 10px;
            gap: 10px;
        }

        .notification-icon {
            width: 36px;
            height: 36px;
            font-size: 18px;
        }

        .notification-title {
            font-size: 12px;
        }

        .notification-message {
            font-size: 11px;
        }

        .notification-header {
            gap: 8px;
        }

        .notification-type-badge {
            font-size: 10px;
            padding: 2px 6px;
        }

        .notification-time {
            font-size: 10px;
        }

        .notification-actions {
            gap: 4px;
        }

        .notification-action-btn {
            padding: 2px 4px;
            font-size: 12px;
        }

        .notifications-list {
            max-height: 500px;
        }

        .empty-state {
            padding: 60px 16px;
        }

        .empty-state i {
            font-size: 48px;
        }

        .loading-state {
            padding: 40px 16px;
        }

        .loading-state i {
            font-size: 36px;
        }
    }
</style>

<script>
    let allNotifications = [];
    let currentFilter = 'all';

    // Load notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();
        setupFilterTabs();
        setupActionButtons();
    });

    function loadNotifications() {
        fetch('/api/notifications', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            allNotifications = data.notifications || [];
            renderNotifications();
            updateCounts();
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            document.getElementById('notificationsList').innerHTML = `
                <div class="empty-state">
                    <i class='bx bx-error-circle'></i>
                    <p>Failed to load notifications</p>
                </div>
            `;
        });
    }

    function renderNotifications() {
        const listEl = document.getElementById('notificationsList');
        
        let filtered = allNotifications;
        if (currentFilter !== 'all') {
            if (currentFilter === 'unread') {
                filtered = allNotifications.filter(n => !n.is_read);
            } else {
                filtered = allNotifications.filter(n => n.type === currentFilter);
            }
        }

        if (filtered.length === 0) {
            listEl.innerHTML = `
                <div class="empty-state">
                    <i class='bx bx-inbox'></i>
                    <p>No notifications to display</p>
                </div>
            `;
            return;
        }

        listEl.innerHTML = filtered.map(notif => {
            const icon = getIconForType(notif.type);
            const time = formatTime(notif.created_at);
            const unreadClass = !notif.is_read ? 'unread' : '';
            
            return `
                <div class="notification-item ${unreadClass} type-${notif.type}" data-notification-id="${notif.id}">
                    <div class="notification-icon">${icon}</div>
                    <div class="notification-content">
                        <div class="notification-header">
                            <h3 class="notification-title">${notif.title}</h3>
                            <span class="notification-type-badge type-${notif.type}">${notif.type}</span>
                        </div>
                        <p class="notification-message">${notif.message}</p>
                        <div class="notification-footer">
                            <span class="notification-time">${time}</span>
                            <div class="notification-actions">
                                ${!notif.is_read ? `<button class="notification-action-btn mark-read" title="Mark as read"><i class='bx bx-check'></i></button>` : ''}
                                <button class="notification-action-btn delete" title="Delete"><i class='bx bx-trash'></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Attach event listeners
        document.querySelectorAll('.mark-read').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const item = btn.closest('.notification-item');
                const notifId = item.dataset.notificationId;
                markAsRead(notifId, item);
            });
        });

        document.querySelectorAll('.notification-action-btn.delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const item = btn.closest('.notification-item');
                const notifId = item.dataset.notificationId;
                deleteNotification(notifId, item);
            });
        });
    }

    function getIconForType(type) {
        switch(type) {
            case 'clamping': return '<i class="bx bx-car"></i>';
            case 'payment': return '<i class="bx bx-wallet"></i>';
            case 'appeal': return '<i class="bx bx-file"></i>';
            case 'system': return '<i class="bx bx-cog"></i>';
            default: return '<i class="bx bx-bell"></i>';
        }
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays < 7) return `${diffDays}d ago`;
        
        return date.toLocaleDateString();
    }

    function setupFilterTabs() {
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                renderNotifications();
            });
        });
    }

    function setupActionButtons() {
        document.getElementById('markAllReadBtn').addEventListener('click', function() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                loadNotifications();
            })
            .catch(error => console.error('Error:', error));
        });

        document.getElementById('clearAllBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to delete all notifications?')) {
                allNotifications.forEach(notif => {
                    fetch(`/notifications/${notif.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                });
                setTimeout(() => loadNotifications(), 500);
            }
        });
    }

    function markAsRead(notifId, item) {
        fetch(`/notifications/${notifId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            item.classList.remove('unread');
            loadNotifications();
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteNotification(notifId, item) {
        fetch(`/notifications/${notifId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            item.style.opacity = '0.5';
            setTimeout(() => {
                item.remove();
                updateCounts();
                if (document.querySelectorAll('.notification-item').length === 0) {
                    loadNotifications();
                }
            }, 300);
        })
        .catch(error => console.error('Error:', error));
    }

    function updateCounts() {
        const unreadCount = allNotifications.filter(n => !n.is_read).length;
        document.getElementById('countAll').textContent = allNotifications.length;
        document.getElementById('countUnread').textContent = unreadCount;
    }

    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
</script>
@endsection
