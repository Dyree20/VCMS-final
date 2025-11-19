@extends('layouts.app')

@section('title', 'Edit Admin Profile')

@php
    // Determine the profile route based on user role
    $userRole = $user->role->name ?? 'Admin';
    $profileRoute = match($userRole) {
        'Front Desk' => 'front-desk.profile',
        'Enforcer' => 'enforcer.profile',
        default => 'admin.profile',
    };
    $profileUpdateRoute = match($userRole) {
        'Front Desk' => 'front-desk.profile.update',
        'Enforcer' => 'profile.update',
        default => 'admin.profile.update',
    };
    // Determine dashboard route
    $dashboardRoute = match($userRole) {
        'Front Desk' => 'front-desk.dashboard',
        'Enforcer' => 'enforcer.dashboard',
        default => 'dashboard',
    };
@endphp

@section('content')
<div class="edit-profile-container">
    <a href="{{ route($profileRoute) }}" class="back-btn">← Back to Profile</a>
    
    <h2>Edit Profile</h2>
    
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

    <form action="{{ route($profileUpdateRoute) }}" method="POST" enctype="multipart/form-data" class="edit-form">
        @csrf
        @method('PUT')
        
        <div class="form-section">
            <h3>Profile Photo</h3>
            <div class="photo-upload">
                <img src="{{ $user->details && $user->details->photo ? asset('storage/' . $user->details->photo) : asset('images/default-avatar.png') }}" alt="Profile" class="preview-image">
                <div class="upload-input">
                    <input type="file" name="photo" accept="image/*" id="photoInput">
                    <label for="photoInput">Choose Photo</label>
                    <small>Recommended: 200x200px, JPG or PNG</small>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Personal Information</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="f_name" value="{{ $user->f_name }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="l_name" value="{{ $user->l_name }}" required class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ $user->email }}" required class="form-control">
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="{{ $user->phone ?? '' }}" class="form-control">
            </div>
        </div>

        <div class="form-section">
            <h3>Additional Information</h3>
            
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="{{ $user->details && $user->details->address ? $user->details->address : '' }}" class="form-control">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ $user->details && $user->details->gender === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $user->details && $user->details->gender === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $user->details && $user->details->gender === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Birth Date</label>
                    <input type="date" name="birthdate" value="{{ $user->details && $user->details->birthdate ? $user->details->birthdate->format('Y-m-d') : '' }}" class="form-control">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route($profileRoute) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
    .edit-profile-container {
        max-width: 700px;
        margin: 30px auto;
        padding: 20px;
    }
    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        color: #4e5de3;
        text-decoration: none;
        font-weight: 600;
    }
    .back-btn:hover {
        color: #3c4bcc;
    }
    h2 {
        color: #333;
        margin-bottom: 24px;
    }
    .form-section {
        margin-bottom: 30px;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 8px;
    }
    .form-section h3 {
        margin: 0 0 16px 0;
        color: #333;
        font-size: 16px;
    }
    .photo-upload {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }
    .preview-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ddd;
    }
    .upload-input {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .upload-input input[type="file"] {
        display: none;
    }
    .upload-input label {
        display: inline-block;
        padding: 10px 16px;
        background: #4e5de3;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }
    .upload-input label:hover {
        background: #3c4bcc;
    }
    .upload-input small {
        color: #999;
        font-size: 12px;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }
    .form-control:focus {
        outline: none;
        border-color: #4e5de3;
        box-shadow: 0 0 0 2px rgba(78, 93, 227, 0.1);
    }
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }
    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-block;
        text-align: center;
        flex: 1;
        transition: all 0.3s;
    }
    .btn-primary {
        background: #4e5de3;
        color: #fff;
    }
    .btn-primary:hover {
        background: #3c4bcc;
    }
    .btn-secondary {
        background: #f0f0f0;
        color: #333;
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
        .photo-upload {
            flex-direction: column;
            align-items: center;
        }
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
