@extends('layouts.app')

@section('title', 'Create Parking Zone')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<style>
#map {
    height: calc(100vh - 140px);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e0e0e0;
    width: 100% !important;
    min-height: 500px;
}

.map-instructions {
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: auto;
    padding: 12px 16px;
    background: #f0f7ff;
    border-left: 4px solid #2196F3;
    border-radius: 4px;
    font-size: 13px;
    color: #1565c0;
    max-width: 400px;
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.map-instructions i {
    margin-right: 8px;
}

.form-hint {
    display: none;
}
</style>

@section('content')
<div class="zone-create-container">
    <div class="create-header">
        <h2>Create New Parking Zone</h2>
        <a href="{{ route('zones.index') }}" class="btn-back">← Back to Zones</a>
    </div>

    <div class="form-content-wrapper">
        <!-- Left Column: Forms -->
        <div class="form-column">
            <form method="POST" action="{{ route('zones.store') }}" class="create-form">
                @csrf

                <div class="form-section">
                    <h3>Zone Information</h3>

                    <!-- Zone Name -->
                    <div class="form-group">
                        <label for="name">Zone Name *</label>
                        <input type="text" id="name" name="name" required class="form-control" placeholder="e.g., Downtown Zone A" value="{{ old('name') }}">
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief description of this parking zone">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <h3>Location</h3>

                    <div class="form-row">
                        <!-- Latitude -->
                        <div class="form-group">
                            <label for="latitude">Latitude *</label>
                            <input type="number" id="latitude" name="latitude" step="0.000001" required class="form-control" placeholder="14.599512" value="{{ old('latitude') }}">
                            @error('latitude')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Longitude -->
                        <div class="form-group">
                            <label for="longitude">Longitude *</label>
                            <input type="number" id="longitude" name="longitude" step="0.000001" required class="form-control" placeholder="121.012345" value="{{ old('longitude') }}">
                            @error('longitude')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Location Hint -->
                    <p class="form-hint">
                        <i class="fa-solid fa-lightbulb" style="margin-right: 8px; color: #ffc107;"></i><strong>Tip:</strong> Get coordinates from <a href="https://maps.google.com" target="_blank">Google Maps</a> or <a href="https://openstreetmap.org" target="_blank">OpenStreetMap</a>
                    </p>
                </div>

                <div class="form-section">
                    <h3>Zone Details</h3>

                    <div class="form-row">
                        <!-- Radius -->
                        <div class="form-group">
                            <label for="radius_meters">Radius (meters) *</label>
                            <input type="number" id="radius_meters" name="radius_meters" min="50" max="5000" required class="form-control" placeholder="500" value="{{ old('radius_meters') }}">
                            @error('radius_meters')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fine Amount -->
                        <div class="form-group">
                            <label for="fine_amount">Fine Amount (₱) *</label>
                            <input type="number" id="fine_amount" name="fine_amount" step="0.01" required class="form-control" placeholder="500.00" value="{{ old('fine_amount') }}">
                            @error('fine_amount')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required class="form-control">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        @error('status')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check"></i> Create Zone
                    </button>
                    <a href="{{ route('zones.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-xmark" style="margin-right: 6px;"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Right Column: Map -->
        <div class="map-column">
            <div id="map"></div>
            <div class="map-instructions">
                <i class="fa-solid fa-map-pin"></i>
                <strong>Map:</strong> Enter coordinates or click on the map to select a location. Drag the marker to adjust.
            </div>
        </div>
    </div>
</div>

<style>
    .zone-create-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .create-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 28px;
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: white;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .create-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .btn-back {
        color: white;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 16px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: white;
    }

    .form-content-wrapper {
        display: grid;
        grid-template-columns: 1fr 1.3fr;
        gap: 0;
        flex: 1;
        overflow: hidden;
    }

    .form-column {
        overflow-y: auto;
        padding: 20px;
        background: #fafbfc;
    }

    .map-column {
        position: relative;
        background: #fff;
        overflow: hidden;
    }

    .create-form {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 28px;
    }

    .form-section {
        margin-bottom: 28px;
    }

    .form-section h3 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin: 0 0 16px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid #f0f0f0;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.3s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #2b58ff;
        box-shadow: 0 0 0 3px rgba(43, 88, 255, 0.1);
    }

    .form-hint {
        font-size: 12px;
        color: #666;
        margin: 12px 0 0 0;
        line-height: 1.5;
    }

    .form-hint a {
        color: #2b58ff;
        text-decoration: none;
        font-weight: 600;
    }

    .form-hint a:hover {
        text-decoration: underline;
    }

    .error-message {
        display: block;
        color: #dc3545;
        font-size: 12px;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 2px solid #f0f0f0;
    }

    .btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #2b58ff;
        color: white;
    }

    .btn-primary:hover {
        background: #1e42cc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 88, 255, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }

    /* Tablet Responsive */
    @media (max-width: 1200px) {
        .form-content-wrapper {
            grid-template-columns: 1fr 1fr;
        }

        #map {
            height: calc(100vh - 140px);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .form-content-wrapper {
            grid-template-columns: 1fr;
            overflow-y: auto;
        }

        .form-column {
            height: auto;
            overflow-y: visible;
        }

        .map-column {
            height: 400px;
            min-height: 400px;
        }

        #map {
            height: 400px;
        }

        .create-header {
            flex-direction: column;
            gap: 12px;
            padding: 16px 20px;
        }

        .create-header h2 {
            font-size: 20px;
        }

        .create-form {
            padding: 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .btn {
            padding: 10px 16px;
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .create-header {
            padding: 12px 16px;
        }

        .create-header h2 {
            font-size: 18px;
        }

        .btn-back {
            padding: 6px 12px;
            font-size: 12px;
        }

        .create-form {
            padding: 16px;
            border-radius: 8px;
        }

        .form-section h3 {
            font-size: 14px;
        }

        .btn {
            padding: 10px 12px;
            font-size: 12px;
        }

        .map-column {
            height: 300px;
        }

        #map {
            height: 300px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const radiusInput = document.getElementById('radius_meters');
    const mapElement = document.getElementById('map');
    
    // Default center (Manila, Philippines)
    const defaultLat = 14.5995;
    const defaultLng = 121.0012;
    
    // Get initial values or use defaults
    const initialLat = parseFloat(latInput.value) || defaultLat;
    const initialLng = parseFloat(lngInput.value) || defaultLng;
    
    // Initialize map
    const map = L.map('map').setView([initialLat, initialLng], 15);
    
    // Enable scroll wheel zoom on the map
    map.scrollWheelZoom.enable();
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Create marker
    let marker = L.marker([initialLat, initialLng], {
        draggable: true,
        title: 'Parking Zone Location'
    }).addTo(map);
    
    // Create circle (for radius visualization)
    let circle = L.circle([initialLat, initialLng], {
        color: '#2196F3',
        fillColor: '#2196F3',
        fillOpacity: 0.2,
        weight: 2,
        radius: parseInt(radiusInput.value) || 500
    }).addTo(map);
    
    // Update map when inputs change
    function updateMap() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        const radius = parseInt(radiusInput.value) || 500;
        
        if (!isNaN(lat) && !isNaN(lng)) {
            const newLatLng = [lat, lng];
            marker.setLatLng(newLatLng);
            circle.setLatLng(newLatLng);
            circle.setRadius(radius);
            map.setView(newLatLng, 15);
        }
    }
    
    // Update marker when dragged
    marker.on('dragend', function() {
        const pos = marker.getLatLng();
        latInput.value = pos.lat.toFixed(6);
        lngInput.value = pos.lng.toFixed(6);
    });
    
    // Update map when inputs change
    latInput.addEventListener('change', updateMap);
    lngInput.addEventListener('change', updateMap);
    radiusInput.addEventListener('change', updateMap);
    
    // Allow clicking on map to set location
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
        
        marker.setLatLng([lat, lng]);
        circle.setLatLng([lat, lng]);
    });
});
</script>

@endsection
