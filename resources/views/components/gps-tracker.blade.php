@section('title', 'Enforcer Location Tracking')

<div class="enforcer-dashboard">
    <!-- GPS Tracker Section -->
    <div id="gps-tracker-container" class="gps-tracker-section">
        <div class="gps-header">
            <h2>📍 Location Tracking</h2>
            <div class="gps-status-container">
                <span id="gps-status-badge" class="status-badge status-offline">OFFLINE</span>
                <span id="gps-tracking-indicator" class="tracking-indicator"></span>
            </div>
        </div>

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
            <label>Set Status:</label>
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
            <p style="color: #999; text-align: center;">Location data will appear here...</p>
        </div>

        <!-- Info Box -->
        <div class="gps-info-box">
            <h4>📝 How to Use:</h4>
            <ul>
                <li>Click "Start Tracking" to enable GPS location sharing</li>
                <li>Your location will be updated every 30 seconds</li>
                <li>You can set your status (Online, On Break, Offline)</li>
                <li>Only visible to authorized administrators</li>
                <li>Make sure location tracking is enabled in Account Settings → Security</li>
            </ul>
        </div>
    </div>

    <style>
        .gps-tracker-section {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
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

        .tracking-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #f0f0f0;
            animation: pulse 2s infinite;
        }

        .tracking-indicator.active {
            background: #4caf50;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.6;
            }
            50% {
                opacity: 1;
            }
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
            .gps-tracker-section {
                padding: 16px;
            }

            .gps-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
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
        }
    </style>
</div>

<script src="{{ asset('js/gps-tracker.js') }}?v={{ time() }}"></script>
