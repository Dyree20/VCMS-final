@extends('dashboards.enforcer')

@section('title', 'GPS Location Tracking')

@section('content')
@php
    $user = auth()->user();
    $user->load('details');
    $profilePhoto = $user->details && $user->details->photo 
        ? asset('storage/' . $user->details->photo) 
        : asset('images/default-avatar.png');
@endphp

<header>
    <h2>GPS Location Tracking</h2>
    <a href="{{ route('enforcer.profile') }}" class="profile-link" title="Back to Profile">
        <img src="{{ $profilePhoto }}" alt="Profile" class="profile-picture">
    </a>
</header>

<!-- GPS Tracker Component -->
<div id="gps-tracker-container" class="gps-tracker-section">
    <div class="gps-header">
        <h2>📍 Real-Time Location Tracking</h2>
        <div class="gps-status-container">
            <span id="gps-status-badge" class="status-badge status-offline">OFFLINE</span>
        </div>
    </div>

    <!-- Status Check -->
    @if(!$user->location_tracking_enabled)
        <div class="warning-box">
            <p>📍 Location tracking is currently <strong>DISABLED</strong></p>
            <p>To use GPS tracking, enable it in <strong>Account Settings → Security → Location Tracking</strong></p>
            <a href="{{ route('account.settings') }}" class="warning-action-btn">Go to Settings</a>
        </div>
    @else
        <!-- GPS Controls -->
        <div class="gps-controls">
            <button id="gps-start-btn" class="gps-btn gps-btn-primary">
                🚀 Start Tracking
            </button>
            <button id="gps-stop-btn" class="gps-btn gps-btn-danger">
                🛑 Stop Tracking
            </button>
        </div>

        <!-- Status Control -->
        <div class="status-control">
            <label>Set Your Status:</label>
            <div class="status-buttons">
                <button class="status-btn status-btn-online" data-status="online">
                    🟢 Online
                </button>
                <button class="status-btn status-btn-break" data-status="on_break">
                    🟡 On Break
                </button>
                <button class="status-btn status-btn-offline" data-status="offline">
                    🔴 Offline
                </button>
            </div>
        </div>

        <!-- Location Display -->
        <div id="gps-location-display" class="location-display">
            <p style="color: #999; text-align: center;">Click "Start Tracking" to begin sharing your location...</p>
        </div>

        <!-- Info Box -->
        <div class="gps-info-box">
            <h4>📝 How GPS Tracking Works:</h4>
            <ul>
                <li>Click "Start Tracking" to enable real-time GPS location sharing</li>
                <li>Your location will be updated every 30 seconds</li>
                <li>You can set your status: Online, On Break, or Offline</li>
                <li>Only authorized administrators can see your location</li>
                <li>Location data includes accuracy in meters</li>
                <li>You can stop tracking at any time</li>
            </ul>
        </div>
    @endif
</div>

<style>
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f0f0f0;
    }

    header h2 {
        margin: 0;
        font-size: 24px;
        color: #333;
    }

    .profile-link {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .profile-picture {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #2b58ff;
    }

    .gps-tracker-section {
        background: white;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .gps-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 12px;
    }

    .gps-header h2 {
        margin: 0;
        color: #333;
        font-size: 20px;
    }

    .gps-status-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .warning-box {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 6px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .warning-box p {
        margin: 8px 0;
        color: #856404;
        font-size: 14px;
    }

    .warning-action-btn {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 16px;
        background: #ffc107;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .warning-action-btn:hover {
        background: #e0a800;
    }

    .gps-controls {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .gps-btn {
        padding: 10px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }

    .gps-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .gps-btn-primary {
        background: #4caf50;
        color: white;
    }

    .gps-btn-primary:hover {
        background: #388e3c;
    }

    .gps-btn-danger {
        background: #f44336;
        color: white;
    }

    .gps-btn-danger:hover {
        background: #d32f2f;
    }

    .status-control {
        margin-bottom: 20px;
        padding: 16px;
        background: #f9f9f9;
        border-radius: 6px;
    }

    .status-control label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .status-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .status-btn {
        padding: 8px 14px;
        border: 2px solid #ddd;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.3s;
    }

    .status-btn:hover {
        border-color: #2b58ff;
        color: #2b58ff;
    }

    .status-btn-online:hover {
        background: #e8f5e9;
        border-color: #4caf50;
        color: #4caf50;
    }

    .status-btn-break:hover {
        background: #fff3e0;
        border-color: #ff9800;
        color: #ff9800;
    }

    .status-btn-offline:hover {
        background: #ffebee;
        border-color: #f44336;
        color: #f44336;
    }

    .location-display {
        background: #f5f5f5;
        padding: 16px;
        border-radius: 6px;
        margin-bottom: 16px;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .location-info {
        width: 100%;
    }

    .location-info p {
        margin: 6px 0;
        font-size: 14px;
        color: #333;
    }

    .location-info strong {
        color: #2b58ff;
    }

    .gps-info-box {
        background: #e3f2fd;
        border-left: 4px solid #2b58ff;
        padding: 16px;
        border-radius: 4px;
        margin-top: 20px;
    }

    .gps-info-box h4 {
        margin: 0 0 10px 0;
        color: #2b58ff;
        font-size: 14px;
    }

    .gps-info-box ul {
        margin: 0;
        padding-left: 20px;
        font-size: 13px;
        color: #333;
        line-height: 1.6;
    }

    .gps-info-box li {
        margin: 6px 0;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-offline {
        background: #ffebee;
        color: #f44336;
    }

    .status-online {
        background: #e8f5e9;
        color: #4caf50;
    }

    .status-on_break {
        background: #fff3e0;
        color: #ff9800;
    }

    @media (max-width: 768px) {
        header {
            margin-bottom: 20px;
        }

        header h2 {
            font-size: 20px;
        }

        .gps-tracker-section {
            padding: 16px;
        }

        .gps-controls {
            width: 100%;
        }

        .gps-btn {
            flex: 1;
            min-width: 120px;
        }

        .status-buttons {
            width: 100%;
        }

        .status-btn {
            flex: 1;
            min-width: 80px;
        }

        .gps-info-box ul {
            padding-left: 18px;
            font-size: 12px;
        }
    }
</style>
@endsection
