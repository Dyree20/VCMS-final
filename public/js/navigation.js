document.addEventListener('DOMContentLoaded', () => {
    const backBtn = document.getElementById('backBtn');
    if (backBtn) {
        backBtn.addEventListener('click', () => {
            window.location.href = '/enforcer/dashboard';
        });
    }

    
});

document.addEventListener("DOMContentLoaded", () => {
    const homeBtn = document.getElementById("homeBtn");
    const notificationsBtn = document.getElementById("notificationsBtn");
    const addClampingBtn = document.getElementById("addClampingBtn");
    const archivesBtn = document.getElementById("archivesBtn");
    const profileBtn = document.getElementById("profileBtn");
    const backBtn = document.getElementById('backBtn');
    const buttons = document.querySelectorAll("nav button");

    // Set active button based on current page URL
    function setActiveButton() {
        const currentPath = window.location.pathname;
        
        // Remove active class from all buttons
        buttons.forEach(btn => btn.classList.remove("active"));
        
        // Set active based on current path (check more specific paths first)
        // Check for profile first (most specific)
        if (currentPath.includes('/enforcer/profile') || currentPath.includes('/profile/edit') || currentPath.includes('/profile/update')) {
            if (profileBtn) profileBtn.classList.add("active");
        } 
        // Check for archives
        else if (currentPath.includes('/enforcer/archives') || currentPath === '/archives') {
            if (archivesBtn) archivesBtn.classList.add("active");
        } 
        // Check for add clamping
        else if (currentPath.includes('/add-clamping')) {
            if (addClampingBtn) addClampingBtn.classList.add("active");
        } 
        // Check for notifications
        else if (currentPath.includes('/notifications') || currentPath === '/notifications') {
            if (notificationsBtn) notificationsBtn.classList.add("active");
        } 
        // Check for home/dashboard (most general, check last - only if exact match)
        else if (currentPath === '/enforcer/dashboard' || currentPath === '/enforcer' || (currentPath === '/' && document.querySelector('nav'))) {
            if (homeBtn) homeBtn.classList.add("active");
        }
        // If no match, don't set any button as active (safer than defaulting to home)
    }

    // Set active button on page load
    setActiveButton();

    // Highlight active button on click (for immediate feedback)
    buttons.forEach(button => {
        button.addEventListener("click", () => {
            buttons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");
        });
    });

    // Navigation actions
    if (homeBtn) {
    homeBtn.addEventListener("click", () => {
        window.location.href = "/enforcer/dashboard";
    });
    }

    if (notificationsBtn) {
    notificationsBtn.addEventListener("click", () => {
        window.location.href = "/notifications";
    });
    }

    if (addClampingBtn) {
    addClampingBtn.addEventListener("click", () => {
        window.location.href = "/add-clamping";
    });
    }

    if (archivesBtn) {
    archivesBtn.addEventListener("click", () => {
        window.location.href = "/enforcer/archives";
    });
    }

    if (profileBtn) {
    profileBtn.addEventListener("click", () => {
        window.location.href = "/enforcer/profile";
    });
    }
});


    // document.querySelectorAll('.filter-btn').forEach(button => {
    //     button.addEventListener('click', () => {
    //     document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    //     button.classList.add('active');
    //     });
    // });
  
    // setInterval(() => {
    //     fetch('/enforcer/summary')
    //         .then(response => response.json())
    //         .then(data => {
    //         document.querySelector('.summary-card.big h2').textContent = data.totalClampings;
    //         document.querySelector('.summary-card.small:nth-child(1) p').textContent = data.pendingCases;
    //         document.querySelector('.summary-card.small:nth-child(2) p').textContent = 
    //             '₱' + parseFloat(data.totalPayments).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    //         });
    //     }, 5000); // refresh every 5 seconds
   

