<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Enforcer Dashboard')</title>

    <link rel="stylesheet" href="{{ asset('styles/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/enforcer-dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @stack('styles')
</head>
<body>
    @php
        $currentUser = auth()->user();
        $currentUser->load('details');
        $profilePhoto = $currentUser->details && $currentUser->details->photo 
            ? asset('storage/' . $currentUser->details->photo) 
            : asset('images/default-avatar.png');
        $trackingEnabled = (bool) ($currentUser->location_tracking_enabled ?? false);
    @endphp

    {{-- Main content --}}
    <div class="child-container" style="padding-bottom: 100px;">
        @yield('content')
    </div>

    {{-- Navigation (shared across pages) --}}
    <nav>
        <button id="homeBtn"><i class="fa-solid fa-house"></i></button>
        <button id="notificationsBtn"><i class="fa-solid fa-bell"></i></button>
        <button class="add" id="addClampingBtn"><i class="fa-solid fa-plus"></i></button>
        <button id="archivesBtn"><i class="fa-solid fa-archive"></i></button>
        <button id="profileBtn"><i class="fa-solid fa-user"></i></button>
    </nav>

    <script src="{{ asset('js/navigation.js') }}"></script>
    <script src="{{ asset('js/clamping-actions.js') }}"></script>
    <script src="{{ asset('js/gps-tracker.js') }}"></script>
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
        @csrf
    </form>
    <script>
        // Store profile photo URL for global updates
        window.profilePhotoUrl = '{{ $profilePhoto }}';
        
        // Function to update all header profile pictures
        function updateHeaderProfilePics(newUrl) {
            document.querySelectorAll('#headerProfilePic').forEach(img => {
                img.src = newUrl;
            });
            window.profilePhotoUrl = newUrl;
        }
        
        // Attach logout handler for enforcer pages with confirmation
        (function () {
            const logoutEls = document.querySelectorAll('.logout, .logout-btn');
            const logoutForm = document.getElementById('logoutForm');
            if (!logoutForm) return;
            logoutEls.forEach(el => {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    const msg = el.dataset.confirmMessage || 'Are you sure you want to log out?';
                    if (confirm(msg)) {
                        logoutForm.submit();
                    }
                });
            });
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof EnforcerGPSTracker === 'undefined') {
                return;
            }

            if (!window.gpsTracker) {
                window.gpsTracker = new EnforcerGPSTracker({
                    updateInterval: 30000,
                    enableHighAccuracy: true,
                    timeout: 10000
                });
            }

            window.gpsTracker.setStatus('online');

            if ({{ $trackingEnabled ? 'true' : 'false' }}) {
                window.gpsTracker.startTracking();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
