@extends('dashboards.enforcer')

@section('content')
<div class="profile-edit-container">
    <a href="{{ route('enforcer.profile') }}" class="back-btn">← Back to Profile</a>
    
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

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label>Profile Photo</label>
            <input type="file" name="photo" accept="image/*" class="form-control">
            <small>Current: {{ $user->details && $user->details->photo ? 'Photo uploaded' : 'No photo' }}</small>
        </div>

        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="f_name" value="{{ $user->f_name }}" required class="form-control">
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="l_name" value="{{ $user->l_name }}" required class="form-control">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" required class="form-control">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone" value="{{ $user->phone ?? '' }}" class="form-control">
        </div>

        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="{{ $user->details && $user->details->address ? $user->details->address : '' }}" class="form-control">
        </div>

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

        <div class="form-actions">
            <button type="submit" class="auth-button">Save Changes</button>
            <a href="{{ route('enforcer.profile') }}" class="auth-button" style="background: #999;">Cancel</a>
        </div>
    </form>
</div>

<style>
    .profile-edit-container {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
    }
    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        color: #4e5de3;
        text-decoration: none;
        font-weight: 600;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #333;
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .form-actions .auth-button {
        flex: 1;
    }
    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
@endsection
