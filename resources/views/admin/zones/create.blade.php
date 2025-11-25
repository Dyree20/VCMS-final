@extends('layouts.app')

@section('title', 'Create Parking Zone')

@section('content')
<div class="zone-create-container">
    <div class="create-header">
        <h2>Create New Parking Zone</h2>
        <a href="{{ route('zones.index') }}" class="btn-back">← Back to Zones</a>
    </div>

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

<style>
    .zone-create-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        max-width: 700px;
        margin: 30px auto;
    }

    .create-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 28px 32px;
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: white;
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

    .create-form {
        padding: 32px;
    }

    .form-section {
        margin-bottom: 32px;
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
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #f0f0f0;
    }

    .btn {
        flex: 1;
        padding: 14px 24px;
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

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .zone-create-container {
            margin: 16px;
            border-radius: 8px;
        }

        .create-header {
            flex-direction: column;
            gap: 12px;
            padding: 20px 16px;
        }

        .create-header h2 {
            font-size: 20px;
        }

        .create-form {
            padding: 20px 16px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .btn {
            padding: 12px 16px;
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .zone-create-container {
            margin: 8px;
        }

        .create-header {
            padding: 16px 12px;
        }

        .create-header h2 {
            font-size: 16px;
        }

        .btn-back {
            padding: 6px 12px;
            font-size: 12px;
        }

        .create-form {
            padding: 16px 12px;
        }

        .form-section h3 {
            font-size: 14px;
        }

        .btn {
            padding: 10px 12px;
            font-size: 12px;
        }
    }
</style>
@endsection
