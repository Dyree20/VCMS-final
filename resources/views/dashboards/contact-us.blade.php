@extends('dashboards.enforcer')

@section('content')
<div class="section-container">
    <a href="{{ route('enforcer.profile') }}" class="back-btn">← Back to Profile</a>
    
    <h2>Contact Us</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
        @csrf
        
        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" required class="form-control" placeholder="What is this about?">
        </div>

        <div class="form-group">
            <label>Message</label>
            <textarea name="message" required class="form-control" rows="6" placeholder="Tell us your concern or question..."></textarea>
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input type="tel" name="phone" class="form-control" placeholder="Optional: Your contact number">
        </div>

        <div class="form-actions">
            <button type="submit" class="auth-button">Send Message</button>
            <a href="{{ route('enforcer.profile') }}" class="auth-button" style="background: #999;">Cancel</a>
        </div>
    </form>

    <div class="contact-info">
        <h3>Other Ways to Reach Us</h3>
        <p><strong>Email:</strong> support@clampingsystem.com</p>
        <p><strong>Phone:</strong> +63 (02) 1234-5678</p>
        <p><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
    </div>
</div>

<style>
    .section-container {
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
    .contact-form {
        margin-bottom: 30px;
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
        font-family: inherit;
    }
    .form-control:focus {
        outline: none;
        border-color: #4e5de3;
        box-shadow: 0 0 0 2px rgba(78, 93, 227, 0.1);
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .form-actions .auth-button {
        flex: 1;
    }
    .contact-info {
        background: #f5f5f5;
        padding: 20px;
        border-radius: 8px;
    }
    .contact-info h3 {
        margin-bottom: 12px;
        color: #333;
    }
    .contact-info p {
        margin: 8px 0;
        color: #666;
    }
    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>
@endsection
