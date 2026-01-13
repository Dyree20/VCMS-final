@extends('dashboards.enforcer')

@section('content')
<!-- ENFORCER EDIT PROFILE VIEW - dashboards/edit-profile.blade.php -->
<div class="edit-profile-wrapper">
    <a href="{{ route('enforcer.profile') }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Back to Profile
    </a>

    <!-- Main Edit Profile Container -->
    <div class="edit-profile-container">
        <!-- Tab Navigation - Top Horizontal Tabs -->
        <div class="edit-tabs-top">
            <button class="edit-tab-btn active" data-section="personal">
                <i class="fa-solid fa-user"></i>
                <span>Personal Information</span>
            </button>
            <button class="edit-tab-btn" data-section="address">
                <i class="fa-solid fa-map-pin"></i>
                <span>Address & Details</span>
            </button>
            <button class="edit-tab-btn" data-section="login">
                <i class="fa-solid fa-lock"></i>
                <span>Login & Password</span>
            </button>
        </div>

        <!-- Forms Container -->
        <div class="edit-forms">
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-exclamation-circle"></i>
                    <div>
                        <strong>Oops! There were some errors:</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-check-circle"></i>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @endif

            <!-- Personal Information Section -->
            <div class="edit-section active" id="personal-section" data-section="personal">
                <div class="section-header">
                    <div>
                        <h2>Personal Information</h2>
                        <p>Update your name, email, and phone number</p>
                    </div>
                    <span class="role-chip">Enforcer</span>
                </div>

                <form action="{{ route('enforcer.profile.update') }}" method="POST" class="edit-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_type" value="personal">

                    <div class="form-grid two-columns">
                        <div class="form-group">
                            <label for="f_name">First Name <span class="required">*</span></label>
                            <input type="text" id="f_name" name="f_name" value="{{ old('f_name', $user->f_name) }}" required placeholder="Enter first name">
                            @error('f_name') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="l_name">Last Name <span class="required">*</span></label>
                            <input type="text" id="l_name" name="l_name" value="{{ old('l_name', $user->l_name) }}" required placeholder="Enter last name">
                            @error('l_name') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-grid two-columns">
                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Enter email address">
                            @error('email') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="e.g., +1 (555) 123-4567">
                            @error('phone') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('enforcer.profile') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> Save Personal Information
                        </button>
                    </div>
                </form>
            </div>

            <!-- Address Section -->
            <div class="edit-section" id="address-section" data-section="address">
                <div class="section-header">
                    <div>
                        <h2>Address & Personal Details</h2>
                        <p>Update your location, address details, and identification</p>
                    </div>
                    <span class="role-chip">Enforcer</span>
                </div>

                <form action="{{ route('enforcer.profile.update') }}" method="POST" class="edit-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_type" value="address">

                    <!-- Personal Details -->
                    <div class="form-section-divider">
                        <h3>Personal Details</h3>
                    </div>

                    <div class="form-grid two-columns">
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-select">
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender', $user->details->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $user->details->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $user->details->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                <option value="prefer_not" {{ old('gender', $user->details->gender ?? '') == 'prefer_not' ? 'selected' : '' }}>Prefer Not to Say</option>
                            </select>
                            @error('gender') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="birth_date">Date of Birth</label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->details->birth_date ?? '') }}" placeholder="Select your birth date">
                            @error('birth_date') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Identification Section -->
                    <div class="form-section-divider">
                        <h3>Identification</h3>
                    </div>

                    <div class="form-grid two-columns">
                        <div class="form-group">
                            <label for="id_type">ID Type</label>
                            <select id="id_type" name="id_type" class="form-select">
                                <option value="">Select ID type</option>
                                <option value="passport" {{ old('id_type', $user->details->id_type ?? '') == 'passport' ? 'selected' : '' }}>Passport</option>
                                <option value="drivers_license" {{ old('id_type', $user->details->id_type ?? '') == 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                                <option value="national_id" {{ old('id_type', $user->details->id_type ?? '') == 'national_id' ? 'selected' : '' }}>National ID</option>
                                <option value="voter_id" {{ old('id_type', $user->details->id_type ?? '') == 'voter_id' ? 'selected' : '' }}>Voter ID</option>
                                <option value="other" {{ old('id_type', $user->details->id_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('id_type') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="id_number">ID Number</label>
                            <input type="text" id="id_number" name="id_number" value="{{ old('id_number', $user->details->id_number ?? '') }}" placeholder="Enter your ID number">
                            @error('id_number') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="form-section-divider">
                        <h3>Address</h3>
                    </div>

                    <div class="form-grid two-columns">
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" id="country" name="country" value="{{ old('country', $user->details->country ?? '') }}" placeholder="Enter country">
                            @error('country') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="city">City/State</label>
                            <input type="text" id="city" name="city" value="{{ old('city', $user->details->city ?? '') }}" placeholder="Enter city or state">
                            @error('city') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-grid two-columns">
                        <div class="form-group">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->details->postal_code ?? '') }}" placeholder="Enter postal code">
                            @error('postal_code') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="address">Street Address</label>
                            <input type="text" id="address" name="address" value="{{ old('address', $user->details->address ?? '') }}" placeholder="Enter street address">
                            @error('address') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio / About</label>
                        <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..." class="form-textarea">{{ old('bio', $user->details->bio ?? '') }}</textarea>
                        @error('bio') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('enforcer.profile') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> Save Address & Personal Details
                        </button>
                    </div>
                </form>
            </div>

            <!-- Login & Password Section -->
            <div class="edit-section" id="login-section" data-section="login">
                <div class="section-header">
                    <div>
                        <h2>Login & Password</h2>
                        <p>Update your credentials and secure your account</p>
                    </div>
                    <span class="role-chip">Enforcer</span>
                </div>

                <form action="{{ route('enforcer.profile.update') }}" method="POST" class="edit-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_type" value="login">

                    <div class="form-grid two-columns">
                        <div class="form-group">
                            <label for="username">Username <span class="required">*</span></label>
                            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required placeholder="Enter username">
                            @error('username') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="login-email">Email Address <span class="required">*</span></label>
                            <input type="email" id="login-email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Enter email address">
                            @error('email') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-note">
                        <i class="fa-solid fa-info-circle"></i>
                        Leave password fields empty if you don't want to change your password.
                    </div>

                    <div class="form-grid two-columns">
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter new password (minimum 8 characters)">
                            @error('password') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat new password">
                            @error('password_confirmation') <span class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('enforcer.profile') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> Save Login Information
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .edit-profile-wrapper {
        max-width: 1000px;
        margin: 20px auto;
        padding: 20px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        color: #2b58ff;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }

    .back-link:hover {
        gap: 12px;
        color: #1e42cc;
    }

    .edit-profile-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    /* Horizontal Tabs */
    .edit-tabs-top {
        display: flex;
        gap: 8px;
        padding: 16px 24px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
    }

    .edit-tab-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: 2px solid transparent;
        border-radius: 8px;
        background: white;
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .edit-tab-btn:hover {
        background: #f3f4f6;
        color: #333;
    }

    .edit-tab-btn.active {
        background: #e3f2fd;
        color: #2b58ff;
        border-color: #2b58ff;
        font-weight: 600;
    }

    .edit-tab-btn i {
        font-size: 15px;
    }

    .edit-forms {
        padding: 32px;
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        gap: 12px;
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fee2e2;
        color: #991b1b;
    }

    .alert-danger i {
        color: #dc2626;
        margin-top: 2px;
    }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #dcfce7;
        color: #166534;
    }

    .alert-success i {
        color: #22c55e;
        margin-top: 2px;
    }

    .alert ul {
        margin: 0;
        padding-left: 20px;
    }

    .alert li {
        margin: 4px 0;
    }

    .edit-section {
        display: none;
        animation: fadeIn 0.3s ease-out;
    }

    .edit-section.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-header h2 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #333;
    }

    .section-header p {
        margin: 4px 0 0 0;
        font-size: 14px;
        color: #9ca3af;
    }

    .role-chip {
        background: #fff5ec;
        color: #ff7a19;
        padding: 8px 14px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 13px;
        white-space: nowrap;
    }

    .edit-form {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .form-grid.two-columns {
        grid-template-columns: repeat(2, 1fr);
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }

    .required {
        color: #dc2626;
    }

    .form-group input,
    .form-textarea,
    .form-select {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 14px;
        font-family: inherit;
        background: #fff;
        transition: all 0.3s;
    }

    .form-group input:focus,
    .form-textarea:focus,
    .form-select:focus {
        outline: none;
        border-color: #2b58ff;
        box-shadow: 0 0 0 3px rgba(43, 88, 255, 0.1);
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px;
        cursor: pointer;
    }

    .form-section-divider {
        margin-top: 24px;
        margin-bottom: 16px;
        padding-top: 20px;
        border-top: 2px solid #f0f0f0;
    }

    .form-section-divider h3 {
        margin: 0 0 12px 0;
        font-size: 16px;
        font-weight: 700;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-divider h3::before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 16px;
        background: #2b58ff;
        border-radius: 2px;
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .error-text {
        font-size: 12px;
        color: #dc2626;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-note {
        background: #f0fdf4;
        border-left: 4px solid #22c55e;
        padding: 12px 14px;
        border-radius: 6px;
        font-size: 13px;
        color: #166534;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2b58ff 0%, #1e42cc 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(43, 88, 255, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(43, 88, 255, 0.4);
    }

    @media (max-width: 768px) {
        .edit-tabs-top {
            flex-direction: column;
        }

        .edit-tab-btn {
            width: 100%;
            justify-content: center;
        }

        .edit-forms {
            padding: 20px;
        }

        .form-grid.two-columns {
            grid-template-columns: 1fr;
        }

        .section-header {
            flex-direction: column;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .edit-profile-wrapper {
            padding: 12px;
            margin: 12px auto;
        }

        .edit-forms {
            padding: 16px;
        }

        .back-link {
            font-size: 13px;
            margin-bottom: 16px;
        }

        .section-header h2 {
            font-size: 18px;
        }

        .section-header {
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
        }

        .role-chip {
            width: fit-content;
            padding: 6px 12px;
            font-size: 12px;
        }

        .form-section-divider {
            margin-top: 16px;
            margin-bottom: 12px;
            padding-top: 12px;
        }

        .form-section-divider h3 {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .form-group label {
            font-size: 12px;
        }

        .form-group input,
        .form-textarea,
        .form-select {
            padding: 10px 12px;
            font-size: 13px;
        }

        .form-grid.two-columns {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .form-actions {
            flex-direction: column;
            gap: 10px;
            padding-top: 16px;
        }

        .btn {
            width: 100%;
            padding: 11px 16px;
            font-size: 13px;
        }

        .form-note {
            padding: 10px 12px;
            font-size: 12px;
            gap: 6px;
        }

        .alert {
            padding: 12px;
            font-size: 13px;
            gap: 8px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.edit-tab-btn');
        const sections = document.querySelectorAll('.edit-section');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const section = this.dataset.section;

                // Remove active from all buttons and sections
                tabBtns.forEach(b => b.classList.remove('active'));
                sections.forEach(sec => sec.classList.remove('active'));

                // Add active to clicked button and corresponding section
                this.classList.add('active');
                document.getElementById(section + '-section').classList.add('active');
            });
        });
    });
</script>

@endsection
