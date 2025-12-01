/**
 * Pending Account Features Restriction Script
 * Disables and hides features for accounts awaiting approval
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if user has pending account status
    const userStatus = document.documentElement.getAttribute('data-user-status');
    const isPending = userStatus === 'pending';

    if (!isPending) return;

    // List of selectors for features to disable
    const featuresToDisable = [
        '[data-feature="clamping"]',
        '[data-feature="payments"]',
        '[data-feature="users"]',
        '[data-feature="teams"]',
        '[data-feature="zones"]',
        '[data-feature="analytics"]',
        '[data-feature="tracking"]',
        '[data-feature="gps"]',
        '[data-feature="archives"]',
        '[data-feature="activity-logs"]',
        'a[href*="/clampings"]',
        'a[href*="/payments"]',
        'a[href*="/users"]',
        'a[href*="/teams"]',
        'a[href*="/zones"]',
        'a[href*="/analytics"]',
        'a[href*="/tracking"]',
        'a[href*="/gps"]',
        'a[href*="/archives"]',
        'a[href*="/activity-logs"]',
    ];

    // Apply disabled state to all matching elements
    featuresToDisable.forEach(selector => {
        document.querySelectorAll(selector).forEach(element => {
            element.classList.add('pending-disabled');
            element.setAttribute('title', 'Available after account approval');
            
            // Prevent navigation for disabled links
            if (element.tagName === 'A') {
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    showPendingNotification();
                });
            }
        });
    });

    // Hide dashboard content (if on dashboard)
    const dashboardContent = document.querySelector('.dashboard-content, .main-content');
    if (dashboardContent && isPending) {
        dashboardContent.classList.add('pending-disabled');
    }
});

/**
 * Show notification that account is pending
 */
function showPendingNotification() {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = 'toast toast-warning';
    toast.innerHTML = `
        <i class="fas fa-clock"></i>
        <span>Your account is pending approval. Profile access only.</span>
    `;
    
    // Add toast styles if not already present
    if (!document.querySelector('style[data-toast-styles]')) {
        const style = document.createElement('style');
        style.setAttribute('data-toast-styles', '');
        style.textContent = `
            .toast {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #fff3cd;
                border: 1px solid #ffc107;
                border-radius: 8px;
                padding: 16px 20px;
                display: flex;
                align-items: center;
                gap: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 10000;
                animation: slideIn 0.3s ease-out;
            }
            .toast i {
                color: #ff9800;
                font-size: 18px;
            }
            .toast span {
                color: #856404;
                font-size: 14px;
                font-weight: 500;
            }
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
        `;
        document.head.appendChild(style);
    }

    document.body.appendChild(toast);

    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease-out reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Add disabled styling to sidebar items
 */
function disableSidebarItems() {
    const navItems = document.querySelectorAll('.nav a');
    const disabledRoutes = [
        '/clampings',
        '/payments',
        '/users',
        '/teams',
        '/zones',
        '/analytics',
        '/tracking',
        '/gps',
        '/archives',
        '/activity-logs',
        '/enforcer/dashboard',
        '/front-desk/dashboard',
    ];

    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && disabledRoutes.some(route => href.includes(route))) {
            item.classList.add('pending-disabled');
            item.parentElement.classList.add('pending-disabled');
        }
    });
}

// Initialize sidebar disabling
if (document.documentElement.getAttribute('data-user-status') === 'pending') {
    disableSidebarItems();
}
