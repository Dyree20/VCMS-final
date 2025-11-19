@extends('layouts.app')

@section('title', 'Edit Parking Zone')

@section('content')
<div class="zone-form-container">
    <a href="{{ route('zones.index') }}" class="back-btn">← Back to Zones</a>
    
    <div class="form-wrapper">
        <h2>Edit Parking Zone</h2>
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('zones.update', $zone->id) }}" method="POST" class="zone-form">
            @csrf
            @method('PUT')
            
            <div class="form-section">
                <h3>Zone Information</h3>
                
                <div class="form-group">
                    <label for="name">Zone Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ $zone->name }}" required class="form-control">
                    @error('name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" class="form-control">{{ $zone->description }}</textarea>
                    @error('description')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Location & Coverage</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="latitude">Latitude <span class="required">*</span></label>
                        <input type="number" id="latitude" name="latitude" value="{{ $zone->latitude }}" step="0.000001" required class="form-control">
                        @error('latitude')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="longitude">Longitude <span class="required">*</span></label>
                        <input type="number" id="longitude" name="longitude" value="{{ $zone->longitude }}" step="0.000001" required class="form-control">
                        @error('longitude')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="radius_meters">Radius (meters) <span class="required">*</span></label>
                        <input type="number" id="radius_meters" name="radius_meters" value="{{ $zone->radius_meters }}" min="1" required class="form-control">
                        @error('radius_meters')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="fine_amount">Fine Amount (₱) <span class="required">*</span></label>
                        <input type="number" id="fine_amount" name="fine_amount" value="{{ $zone->fine_amount }}" min="0" step="0.01" required class="form-control">
                        @error('fine_amount')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" required class="form-control">
                        <option value="active" {{ $zone->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $zone->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="maintenance" {{ $zone->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('status')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="helper-links">
                    <small>
                        Need help finding coordinates? 
                        <a href="https://www.google.com/maps" target="_blank">Google Maps</a> • 
                        <a href="https://www.openstreetmap.org" target="_blank">OpenStreetMap</a>
                    </small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Zone</button>
                <a href="{{ route('zones.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
    .zone-form-container {
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
    }

    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        color: #2b58ff;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }

    .back-btn:hover {
        color: #1e42cc;
    }

    .form-wrapper {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    h2 {
        color: #333;
        margin-bottom: 24px;
        font-size: 24px;
    }

    .form-section {
        margin-bottom: 32px;
    }

    .form-section h3 {
        color: #333;
        font-size: 16px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f0f0;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .required {
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #2b58ff;
        box-shadow: 0 0 0 3px rgba(43, 88, 255, 0.1);
    }

    .error-text {
        display: block;
        color: #dc3545;
        font-size: 12px;
        margin-top: 4px;
    }

    .helper-links {
        margin-top: 12px;
        padding: 12px;
        background: #f0f7ff;
        border-radius: 6px;
        font-size: 12px;
    }

    .helper-links a {
        color: #2b58ff;
        text-decoration: none;
        font-weight: 600;
    }

    .helper-links a:hover {
        text-decoration: underline;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #f0f0f0;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        transition: all 0.2s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: #fff;
        flex: 1;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1e42cc 0%, #152d99 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(43, 88, 255, 0.3);
    }

    .btn-secondary {
        background: #f0f0f0;
        color: #333;
        flex: 1;
    }

    .btn-secondary:hover {
        background: #e0e0e0;
    }

    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 16px;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    @media (max-width: 768px) {
        .zone-form-container {
            padding: 10px;
        }

        .form-wrapper {
            padding: 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        h2 {
            font-size: 20px;
        }

        .form-section h3 {
            font-size: 14px;
        }
    }
</style>
@endsection
